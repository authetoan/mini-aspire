<?php
declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\DTOs\Responses\AuthenticatedUserVO;
use App\Domain\Entities\User\IUserRepository;
use App\Domain\Entities\User\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthService
{
    public function __construct(
        private readonly IUserRepository $userRepository
    ) {
    }
    /**
     * @throws Exception
     */
    public function login(Request $request): AuthenticatedUserVO
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            throw new HttpException(401, $validator->errors()->first());
        }

        /** @var User $user */
        $user = $this->userRepository->findOneByEmailAndPassword($credentials['email'], $credentials['password']);

        if (!$user) {
            throw new HttpException(401, 'Invalid credentials');
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return new AuthenticatedUserVO(
            id: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail(),
            token: $token
        );
    }

    public function getAuthenticatedUser(): User
    {
        /** @var User $user */
        $user = auth()->user();
        return $user;
    }
}
