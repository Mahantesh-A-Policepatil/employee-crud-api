<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthController
 *
 * Handles all authentication operations including registration, login,
 * logout, and user profile management. Uses repository pattern for
 * database operations and form requests for validation.
 *
 * @package App\Http\Controllers\API
 */
class AuthController extends Controller
{
    /**
     * User repository instance for database operations.
     *
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * Role repository instance for role operations.
     *
     * @var RoleRepository
     */
    private RoleRepository $roleRepository;

    /**
     * Create a new AuthController instance.
     *
     * @param UserRepository $userRepository The user repository
     * @param RoleRepository $roleRepository The role repository
     */
    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Format user response payload with roles and permissions.
     *
     * Loads all related roles and permissions for a user and returns
     * a formatted array suitable for API responses.
     *
     * @param mixed $user The user model instance
     *
     * @return array User data with roles and permissions
     */
    private function userPayload($user): array
    {
        $user->loadMissing('roles', 'permissions');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name')->values(),
            'permissions' => $user->getAllPermissions()->pluck('name')->values(),
        ];
    }

    /**
     * Handle user registration.
     *
     * Creates a new user account with validated credentials and assigns
     * the default 'non-admin' role. Returns authentication token.
     *
     * @param RegisterRequest $request The validated registration request
     *
     * @return JsonResponse Registration success response with token and user data
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userRepository->create([
            'name' => $request->validated()['name'],
            'email' => $request->validated()['email'],
            'password' => Hash::make($request->validated()['password']),
        ]);

        $nonAdminRole = $this->roleRepository->firstOrCreate('non-admin', 'web');
        $user->assignRole($nonAdminRole);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $this->userPayload($user),
        ], 201);
    }

    /**
     * Handle user login.
     *
     * Authenticates user credentials and returns authentication token
     * if credentials are valid. Throws ValidationException on failure.
     *
     * @param LoginRequest $request The validated login request
     *
     * @return JsonResponse Login success response with token and user data
     *
     * @throws ValidationException If credentials are incorrect
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $this->userRepository->findByEmail($validated['email']);

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $this->userPayload($user),
        ]);
    }

    /**
     * Handle user logout.
     *
     * Revokes the current API token, effectively logging out the user.
     *
     * @param Request $request The HTTP request
     *
     * @return JsonResponse Logout success response
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }

    /**
     * Retrieve current authenticated user information.
     *
     * Returns the currently authenticated user with their roles and permissions.
     *
     * @param Request $request The HTTP request
     *
     * @return array User data with roles and permissions
     */
    public function user(Request $request): array
    {
        return $this->userPayload($request->user());
    }

    /**
     * Update current user's profile information.
     *
     * Updates user's name, email, and optionally password.
     * Email must remain unique and password must be confirmed if provided.
     *
     * @param UpdateUserRequest $request The validated update request
     *
     * @return JsonResponse User update success response
     */
    public function updateUser(UpdateUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user = $this->userRepository->update($user->id, $updateData);

        return response()->json([
            'message' => 'User information updated successfully.',
            'user' => $this->userPayload($user),
        ]);
    }
}
