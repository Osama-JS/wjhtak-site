<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    #[OA\Post(
        path: "/api/register",
        summary: "Register a new customer",
        operationId: "registerCustomer",
        description: "Registers a new customer and sends an OTP to their email.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["first_name", "last_name", "email", "phone", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "John"),
                    new OA\Property(property: "last_name", type: "string", example: "Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "phone", type: "string", example: "+1234567890"),
                    new OA\Property(property: "country_code", type: "string", example: "+1"),
                    new OA\Property(property: "city", type: "string", example: "New York"),
                    new OA\Property(property: "gender", type: "string", example: "male"),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", example: "1990-01-01"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "Secret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful registration",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Registration successful. Please verify your email with the OTP sent."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "otp_code", type: "string", example: "123456")
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'country_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'country_code' => $request->country_code,
            'city' => $request->city,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'user_type' => User::TYPE_CUSTOMER,
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send OTP via Email
        $this->mailService->sendVerificationOtp($user, $otp);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(false, __('Registration successful. Please verify your email with the OTP sent.'), [
            'access_token' => $token,
            'otp_code' => $otp
        ], null, 200);
    }

    #[OA\Post(
        path: "/api/verify-otp",
        summary: "Verify account using OTP",
        operationId: "verifyOtp",
        description: "Verifies the customer account using the OTP code sent to their email.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["otp_code", "fcm_token", "device_type"],
                properties: [
                    new OA\Property(property: "otp_code", type: "string", example: "123456"),
                    new OA\Property(property: "fcm_token", type: "string", example: "fcm_token_here..."),
                    new OA\Property(property: "device_type", type: "string", enum: ["android", "ios"], example: "android"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Account verified successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Account verified successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                            new OA\Property(property: "user", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "first_name", type: "string", example: "John"),
                                new OA\Property(property: "last_name", type: "string", example: "Doe"),
                                new OA\Property(property: "email", type: "string", example: "user@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "123456789"),
                                new OA\Property(property: "country_code", type: "string", example: "+1"),
                                new OA\Property(property: "profile_photo_url", type: "string", example: "http://example.com/storage/profile_photos/photo.jpg"),
                                new OA\Property(property: "is_active", type: "boolean", example: true),
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Invalid or expired OTP",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired OTP code.")
                    ]
                )
            ),
        ]
    )]
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|size:6',
            'fcm_token' => 'required|string',
            'device_type' => 'required|string|in:android,ios',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = $request->user();

        if ($user->otp_code !== $request->otp_code || $user->otp_expires_at->isPast()) {
            return $this->apiResponse(true, __('Invalid or expired OTP code.'), null, null, 422);
        }

        $user->email_verified_at = Carbon::now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->fcm_token = $request->fcm_token;
        $user->device_type = $request->device_type;
        $user->save();

        // Send Welcome Email
        $this->mailService->sendWelcomeEmail($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(false, __('Account verified successfully.'), [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    #[OA\Post(
        path: "/api/resend-otp",
        summary: "Resend verification OTP",
        operationId: "resendOtp",
        description: "Resends a new OTP code to the customer's email if the account is not verified.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "OTP resent successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "OTP has been resent to your email.")
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Account already verified",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Account is already verified.")
                    ]
                )
            ),
        ]
    )]
    public function resendOtp(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            return $this->apiResponse(true, __('Account is already verified.'), null, null, 403);
        }

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // Send OTP via Email
        $this->mailService->sendVerificationOtp($user, $otp);

        return $this->apiResponse(false, __('OTP has been resent to your email.'));
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Login customer",
        operationId: "loginCustomer",
        description: "Registers a session for the customer and returns an access token.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password", "fcm_token", "device_type"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "Secret123"),
                    new OA\Property(property: "fcm_token", type: "string", example: "fcm_token_here..."),
                    new OA\Property(property: "device_type", type: "string", enum: ["android", "ios"], example: "android"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful login",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Login successful."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                            new OA\Property(property: "user", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "first_name", type: "string", example: "John"),
                                new OA\Property(property: "last_name", type: "string", example: "Doe"),
                                new OA\Property(property: "email", type: "string", example: "user@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "123456789"),
                                new OA\Property(property: "country_code", type: "string", example: "+1"),
                                new OA\Property(property: "profile_photo_url", type: "string", example: "http://example.com/storage/profile_photos/photo.jpg"),
                                new OA\Property(property: "is_active", type: "boolean", example: true),
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid credentials",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid login credentials.")
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: "Account not verified",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Please verify your account first."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "verified", type: "boolean", example: false)
                        ])
                    ]
                )
            ),
        ]
    )]
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'fcm_token' => 'required|string',
            'device_type' => 'required|string|in:android,ios',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->apiResponse(true, __('Invalid login credentials.'), null, null, 401);
        }

        if (!$user->email_verified_at) {
            return $this->apiResponse(true, __('Please verify your account first.'), ['verified' => false], null, 403);
        }

        $user->update([
            'fcm_token' => $request->fcm_token,
            'device_type' => $request->device_type,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(false, __('Login successful.'), [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    #[OA\Get(
        path: "/api/check-token",
        summary: "Check token validity",
        operationId: "checkToken",
        description: "Checks if the current authentication token is valid and returns user info.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Token is valid",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Token is valid."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "first_name", type: "string", example: "John"),
                                new OA\Property(property: "last_name", type: "string", example: "Doe"),
                                new OA\Property(property: "email", type: "string", example: "user@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "123456789"),
                                new OA\Property(property: "country_code", type: "string", example: "+1"),
                                new OA\Property(property: "profile_photo_url", type: "string", example: "http://example.com/storage/profile_photos/photo.jpg"),
                                new OA\Property(property: "is_active", type: "boolean", example: true),
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function checkToken(Request $request)
    {
        return $this->apiResponse(false, __('Token is valid.'), [
            'user' => $request->user()
        ]);
    }

    #[OA\Get(
        path: "/api/profile",
        summary: "Get customer profile",
        operationId: "getCustomerProfile",
        description: "Returns the authenticated customer's profile information.",
        tags: ["Profile"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Profile retrieved successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "first_name", type: "string", example: "John"),
                                new OA\Property(property: "last_name", type: "string", example: "Doe"),
                                new OA\Property(property: "email", type: "string", example: "user@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "123456789"),
                                new OA\Property(property: "country_code", type: "string", example: "+1"),
                                new OA\Property(property: "profile_photo_url", type: "string", example: "http://example.com/storage/profile_photos/photo.jpg"),
                                new OA\Property(property: "is_active", type: "boolean", example: true),
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function profile(Request $request)
    {
        return $this->apiResponse(false, __('Profile retrieved successfully.'), [
            'user' => $request->user()
        ]);
    }

    #[OA\Post(
        path: "/api/profile/update",
        summary: "Update customer profile",
        operationId: "updateCustomerProfile",
        description: "Updates the authenticated customer's profile details.",
        tags: ["Profile"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "first_name", type: "string", example: "John"),
                        new OA\Property(property: "last_name", type: "string", example: "Doe"),
                        new OA\Property(property: "city", type: "string", example: "New York"),
                        new OA\Property(property: "gender", type: "string", example: "male"),
                        new OA\Property(property: "date_of_birth", type: "string", format: "date", example: "1990-01-01"),
                        new OA\Property(property: "address", type: "string", example: "123 Main St"),
                        new OA\Property(property: "profile_photo", type: "string", format: "binary"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Profile updated successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "user", type: "object", properties: [
                                new OA\Property(property: "id", type: "integer", example: 1),
                                new OA\Property(property: "first_name", type: "string", example: "John"),
                                new OA\Property(property: "last_name", type: "string", example: "Doe"),
                                new OA\Property(property: "email", type: "string", example: "user@example.com"),
                                new OA\Property(property: "phone", type: "string", example: "123456789"),
                                new OA\Property(property: "country_code", type: "string", example: "+1"),
                                new OA\Property(property: "profile_photo_url", type: "string", example: "http://example.com/storage/profile_photos/photo.jpg"),
                                new OA\Property(property: "is_active", type: "boolean", example: true),
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'city' => 'sometimes|string|max:100',
            'gender' => 'sometimes|string|in:male,female,other',
            'date_of_birth' => 'sometimes|date',
            'address' => 'sometimes|string|max:500',
            'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $data = $request->only(['first_name', 'last_name', 'city', 'gender', 'date_of_birth', 'address']);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($user->profile_photo) {
                @unlink(public_path('storage/' . $user->profile_photo));
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        return $this->apiResponse(false, __('Profile updated successfully.'), [
            'user' => $user->fresh()
        ]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Logout customer",
        operationId: "logoutCustomer",
        description: "Revokes the current access token and ends the session.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logged out successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Logged out successfully.")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->apiResponse(false, __('Logged out successfully.'));
    }

    #[OA\Post(
        path: "/api/forgot-password",
        summary: "Request password reset OTP",
        operationId: "forgotPassword",
        description: "Sends a password reset OTP to the user's email.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "OTP sent successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password reset code sent to your email."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "access_token", type: "string", example: "1|abc..."),
                            new OA\Property(property: "otp_code", type: "string", example: "123456")
                        ])
                    ]
                )
            ),
        ]
    )]
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = User::where('email', $request->email)->first();

        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        $this->mailService->sendPasswordResetOtp($user, $otp);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(false, __('Password reset code sent to your email.'), [
            'access_token' => $token,
            'otp_code' => $otp

        ]);
    }

    #[OA\Post(
        path: "/api/reset-password",
        summary: "Reset password using OTP",
        operationId: "resetPassword",
        description: "Resets the user's password using the OTP code sent to their email.",
        tags: ["Authentication"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["otp_code", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "otp_code", type: "string", example: "123456"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "NewSecret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "NewSecret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Password reset successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password has been reset successfully.")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Invalid or expired OTP",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid or expired OTP code.")
                    ]
                )
            ),
        ]
    )]
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = $request->user();

        if ($user->otp_code !== $request->otp_code || Carbon::now()->gt($user->otp_expires_at)) {
            return $this->apiResponse(true, __('Invalid or expired OTP code.'), null, null, 422);
        }

        $user->password = Hash::make($request->password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Revoke the temporary token
        $user->currentAccessToken()->delete();

        return $this->apiResponse(false, __('Password has been reset successfully.'));
    }

    #[OA\Post(
        path: "/api/profile/change-password",
        summary: "Change user password",
        operationId: "changePassword",
        description: "Changes the authenticated user's password after verifying the old password.",
        tags: ["Profile"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["old_password", "new_password", "new_password_confirmation"],
                properties: [
                    new OA\Property(property: "old_password", type: "string", format: "password", example: "OldSecret123"),
                    new OA\Property(property: "new_password", type: "string", format: "password", example: "NewSecret123"),
                    new OA\Property(property: "new_password_confirmation", type: "string", format: "password", example: "NewSecret123"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Password changed successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Password changed successfully.")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error or invalid old password",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Invalid old password.")
                    ]
                )
            ),
        ]
    )]
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->apiResponse(true, __('Invalid old password.'), null, null, 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->apiResponse(false, __('Password changed successfully.'));
    }

    /**
     * Update FCM Token for push notifications
     */
    #[OA\Post(
        path: "/api/update-fcm-token",
        summary: "Update user FCM token",
        operationId: "updateFcmToken",
        description: "Updates the authenticated user's Firebase Cloud Messaging token for push notifications.",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["fcm_token"],
                properties: [
                    new OA\Property(property: "fcm_token", type: "string", example: "fcm_token_here..."),
                    new OA\Property(property: "device_type", type: "string", enum: ["android", "ios"], example: "android"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "FCM token updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "FCM token updated successfully.")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function updateFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
            'device_type' => 'nullable|string|in:android,ios',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $user = $request->user();
        $user->update([
            'fcm_token' => $request->fcm_token,
            'device_type' => $request->device_type,
        ]);

        return $this->apiResponse(false, __('FCM token updated successfully.'));
    }
}
