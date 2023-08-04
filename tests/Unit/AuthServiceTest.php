<?php

namespace Tests\Unit;

use App\Domain\Entities\User\IUserRepository;
use App\Domain\Entities\User\User;
use App\Domain\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    public function testLoginSuccess(): void
    {
        $userRepo = $this->createMock(IUserRepository::class);
        $user = $this->createMock(User::class);
        $user->expects($this->once())->method('getId')->willReturn(1);
        $user->expects($this->once())->method('getName')->willReturn('test');
        $user->expects($this->once())->method('getEmail')->willReturn('test@example.com');
        $user->expects($this->once())->method('createToken')->willReturn(
            (object) ['plainTextToken' => 'token']
        );
        $userRepo->expects($this->once())->method('findOneByEmailAndPassword')->willReturn($user);
        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('only')->willReturn([
            'email' => 'test@example.com',
            'password' => '123456',
        ]);
        Validator::shouldReceive('make')->andReturn(Mockery::mock([
            'passes' => true,
            'fails' => false,
            'errors' => []
        ]));

        $authService = new AuthService($userRepo);

        $response = $authService->login($request);

        $this->assertEquals(1, $response->id);
        $this->assertEquals('test', $response->name);
        $this->assertEquals('test@example.com', $response->email);
        $this->assertEquals('token', $response->token);
    }
}
