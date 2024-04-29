<?php
namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{


/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", format="integer", example=1),
 *     @OA\Property(property="username", type="string", example="john_doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="job_title", type="string", example="Software Developer"),
 *     @OA\Property(property="phone", type="string", example="1234567890"),
 *     @OA\Property(property="birthdate", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="cv", type="string", example="/path/to/cv.pdf"),
 *     @OA\Property(property="profile_picture", type="string", example="/path/to/profile_picture.jpg"),
 *     @OA\Property(property="password", type="string", format="password", example="password"),
 *     @OA\Property(property="role", type="string", example="user"),
 * )
 *
 * @OA\Schema(
 *     schema="UserRequest",
 *     title="UserRequest",
 *     description="User request model",
 *     @OA\Property(property="username", type="string", example="john_doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="job_title", type="string", example="Software Developer"),
 *     @OA\Property(property="phone", type="string", example="1234567890"),
 *     @OA\Property(property="birthdate", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="cv", type="string", format="file", example="cv.pdf"),
 *     @OA\Property(property="profile_picture", type="string", format="file", example="profile_picture.jpg"),
 *     @OA\Property(property="password", type="string", format="password", example="password"),
 *     required={"username","email","first_name","last_name","job_title","phone","birthdate",
 *     "cv","profile_picture","password"},
 *     @OA\Xml(name="UserRequest")
 * )

    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"User Management"},
     *     summary="List all users",
     *     description="This endpoint allows you to list all users.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function index(): string
    {
        $users = User::all();
        return $users->toJson();
    }

    /**
     * @OA\Post(
     *     path="/api/signup",
     *     tags={"User Management"},
     *     summary="Create a new user",
     *     description="This endpoint allows you to create a new user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $userName = $validatedData['username'];
        $cvPath = null;
        $profilePicturePath = null;

        if ($request->hasFile('cv')) {
            $cvPath = Storage::disk('s3')->put("/cvs/$userName.pdf", $request->file('cv'));
        }
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = Storage::disk('s3')->put("/photos/$userName.jpg",
                $request->file('profile_picture'));
        }

        $user = User::create([
            'username' => $userName,
            'email' => $validatedData['email'],
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'job_title' => $validatedData['job_title'],
            'phone' => $validatedData['phone'],
            'birthdate' => $validatedData['birthdate'],
            'cv' => $cvPath,
            'profile_picture' => $profilePicturePath,
            'password' => bcrypt($validatedData['password']),
        ]);

        // Hide the password field from the JSON response
        $user->makeHidden('password');
        $user->makeHidden('remember_token');

        // Generate the token and return the response
        $token = $user->createToken('authToken')->plainTextToken;
        $responseData = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'job_title' => $user->job_title,
            'phone' => $user->phone,
            'birthdate' => $user->birthdate,
            'cv' => $user->cv,
            'profile_picture' => $user->profile_picture,
        ];
        return response()->json($responseData, 201)->header('Authorization', $token);
    }




    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"User Management"},
     *     summary="Get user by ID",
     *     description="This endpoint allows you to get details of a specific user.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"User Management"},
     *     summary="Update user by ID",
     *     description="This endpoint allows you to update details of a specific user.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UserRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());
        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"User Management"},
     *     summary="Delete user by ID",
     *     description="This endpoint allows you to delete a specific user.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(["message" => "User deleted successfully"], 204);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"User Management"},
     *     summary="Authenticate user",
     *     description="This endpoint allows a user to log in by providing their email and password.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", description="Access token"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if user is authenticated
            if ($user) {
                $token = $user->createToken('authToken')->plainTextToken;
                return response()->json($user, 200)->header('Authorization', $token);
            } else {
                // User is not authenticated, handle accordingly
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

}
