<?php

namespace Webkul\Security\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\Unauthenticated;
use RuntimeException;
use Webkul\Security\Models\User;
use Webkul\Security\Services\LoginProtection;
use Webkul\Security\Services\SecurityAuditLogger;

#[Group('Security API Management')]
#[Subgroup('Authentication', 'Handle user authentication')]
class AuthController extends Controller
{
    #[Endpoint('Login', 'Authenticate user and generate API token')]
    #[Unauthenticated]
    #[BodyParam('email', 'string', 'User email address', required: true, example: 'admin@example.com')]
    #[BodyParam('password', 'string', 'User password', required: true, example: 'password')]
    #[Response(status: 200, description: 'Login successful', content: '{"message": "Login successful", "token": "1|abcd1234efgh5678ijkl...", "token_type": "Bearer", "user": {"id": 1, "name": "Admin User", "email": "admin@example.com"}}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"email": ["The email field is required."], "password": ["The password field is required."]}}')]
    #[Response(status: 401, description: 'Invalid credentials', content: '{"message": "The provided credentials are incorrect."}')]
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $protection = app(LoginProtection::class);
        $email = (string) $request->email;

        try {
            $protection->assertNotBlocked();
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages([
                'email' => [$exception->getMessage()],
            ]);
        }

        if ($protection->tooManyAttempts($email)) {
            throw ValidationException::withMessages([
                'email' => [__('security::login-protection.errors.throttled', [
                    'seconds' => max(1, $protection->availableIn($email)),
                ])],
            ]);
        }

        $user = User::whereRaw(db_dialect()->caseInsensitiveEquals('email'), [$email])->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $protection->recordFailure($email);
            app(SecurityAuditLogger::class)->logLoginFailed($email, 'sanctum');

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $protection->recordSuccess($user->email);
        app(SecurityAuditLogger::class)->logLoginSucceeded($user, 'sanctum');

        return response()->json([
            'message'    => 'Login successful',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    #[Endpoint('Logout', 'Revoke current API token')]
    #[Authenticated]
    #[Response(status: 200, description: 'Logout successful', content: '{"message": "Logout successful"}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user instanceof User) {
            app(SecurityAuditLogger::class)->logLogout($user, 'sanctum');
        }

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }
}
