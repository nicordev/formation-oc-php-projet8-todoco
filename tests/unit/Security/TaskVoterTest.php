<?php

namespace App\Tests\unit\Security;


use App\Entity\Task;
use App\Entity\User;
use App\Security\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TaskVoterTest extends TestCase
{
    /*
     * Delete
     */

    public function testVote_delete_noUser()
    {
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method("getUser");
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $task = new Task();
        $voter = new TaskVoter($authorizationChecker);
        $result = $voter->vote($token, $task, [TaskVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVote_delete_admin()
    {
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method("getUser")
            ->willReturn(
                (new User)->setRoles(["ROLE_ADMIN"])
            );
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method("isGranted")
            ->with("ROLE_ADMIN")
            ->willReturn(true);
        $task = new Task();
        $voter = new TaskVoter($authorizationChecker);
        $result = $voter->vote($token, $task, [TaskVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testVote_delete_author()
    {
        $author = (new User)->setRoles(["ROLE_USER"])
            ->setUsername("test_author");
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method("getUser")
            ->willReturn($author);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method("isGranted")
            ->with("ROLE_ADMIN")
            ->willReturn(false);
        $task = (new Task())->setAuthor($author);
        $voter = new TaskVoter($authorizationChecker);
        $result = $voter->vote($token, $task, [TaskVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testVote_delete_notAuthor()
    {
        $user = (new User)->setRoles(["ROLE_USER"])
            ->setUsername("test_user");
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method("getUser")
            ->willReturn($user);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method("isGranted")
            ->with("ROLE_ADMIN")
            ->willReturn(false);
        $task = (new Task())->setAuthor((new User)->setUsername("test_author"));
        $voter = new TaskVoter($authorizationChecker);
        $result = $voter->vote($token, $task, [TaskVoter::DELETE]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }
    
    /*
     * Edit
     */

    public function testVote_edit_noUser()
    {
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method("getUser");
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $task = new Task();
        $voter = new TaskVoter($authorizationChecker);
        $result = $voter->vote($token, $task, [TaskVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVote_edit_admin()
    {
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method("getUser")
            ->willReturn(
                (new User)->setRoles(["ROLE_ADMIN"])
            );
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method("isGranted")
            ->with("ROLE_ADMIN")
            ->willReturn(true);
        $task = new Task();
        $voter = new TaskVoter($authorizationChecker);
        $result = $voter->vote($token, $task, [TaskVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testVote_edit_author()
    {
        $author = (new User)->setRoles(["ROLE_USER"])
            ->setUsername("test_author");
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method("getUser")
            ->willReturn($author);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method("isGranted")
            ->with("ROLE_ADMIN")
            ->willReturn(false);
        $task = (new Task())->setAuthor($author);
        $voter = new TaskVoter($authorizationChecker);
        $result = $voter->vote($token, $task, [TaskVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testVote_edit_notAuthor()
    {
        $user = (new User)->setRoles(["ROLE_USER"])
            ->setUsername("test_user");
        $token = $this->createMock(TokenInterface::class);
        $token->expects($this->once())
            ->method("getUser")
            ->willReturn($user);
        $authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $authorizationChecker->method("isGranted")
            ->with("ROLE_ADMIN")
            ->willReturn(false);
        $task = (new Task())->setAuthor((new User)->setUsername("test_author"));
        $voter = new TaskVoter($authorizationChecker);
        $result = $voter->vote($token, $task, [TaskVoter::EDIT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }
}