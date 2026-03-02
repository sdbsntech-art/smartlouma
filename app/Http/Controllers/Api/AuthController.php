<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\WelcomeMail;
use App\Mail\ProducerPendingMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ── Inscription ─────────────────────────────────────────────
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:consumer,producer',
            'company'  => 'nullable|string|max:100',
            'zone'     => 'nullable|string|max:50',
        ]);

        $status = $data['role'] === 'producer' ? 'pending' : 'active';

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'role'     => $data['role'],
            'status'   => $status,
            'company'  => $data['company'] ?? null,
            'zone'     => $data['zone'] ?? null,
        ]);

        // Email de bienvenue à l'utilisateur
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            logger('Welcome mail failed: ' . $e->getMessage());
        }

        // Si producteur → notifier admin
        if ($user->isProducer()) {
            try {
                Mail::to(config('app.admin_email'))->send(new ProducerPendingMail($user));
            } catch (\Exception $e) {
                logger('Producer pending mail failed: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Inscription envoyée. En attente d\'approbation par l\'administrateur.',
                'pending' => true,
                'user'    => $this->userResource($user),
            ], 201);
        }

        $token = $user->createToken('sl-token')->plainTextToken;

        return response()->json([
            'message' => 'Compte créé avec succès. Bienvenue sur SMART-LOUMA !',
            'token'   => $token,
            'user'    => $this->userResource($user),
        ], 201);
    }

    // ── Connexion ──────────────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Vérification admin (depuis config)
        $adminEmail = config('app.admin_email');
        $adminPwd   = config('app.admin_password');

        if (
            strtolower($data['email']) === strtolower($adminEmail) &&
            $data['password'] === $adminPwd
        ) {
            // Créer/récupérer user admin en base
            $admin = User::firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name'     => config('app.admin_name'),
                    'password' => Hash::make($adminPwd),
                    'role'     => 'admin',
                    'status'   => 'active',
                ]
            );

            $token = $admin->createToken('sl-admin-token', ['*'])->plainTextToken;

            return response()->json([
                'token' => $token,
                'user'  => $this->userResource($admin),
            ]);
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email ou mot de passe incorrect.'],
            ]);
        }

        if ($user->isPending()) {
            return response()->json([
                'error' => 'Votre compte est en attente d\'approbation par l\'administrateur.',
            ], 403);
        }

        if ($user->status === 'suspended') {
            return response()->json([
                'error' => 'Votre compte a été suspendu. Contactez l\'administration.',
            ], 403);
        }

        $user->tokens()->delete(); // invalider anciens tokens
        $token = $user->createToken('sl-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $this->userResource($user),
        ]);
    }

    // ── Déconnexion ────────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    // ── Profil courant ─────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        return response()->json($this->userResource($request->user()));
    }

    // ── Formatage user ─────────────────────────────────────────
    private function userResource(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'phone'      => $user->phone,
            'role'       => $user->role,
            'status'     => $user->status,
            'company'    => $user->company,
            'zone'       => $user->zone,
            'created_at' => $user->created_at?->toISOString(),
        ];
    }
}
