<?php
/**
 * This file is part of CaptainHook.
 *
 * (c) Sebastian Feldmann <sf@sebastian.feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace CaptainHook\App\Hook\Message\Action;

use CaptainHook\App\Config;
use CaptainHook\App\Console\IO\NullIO;
use CaptainHook\App\Git\DummyRepo;
use CaptainHook\App\Hook\Message\Rule\CapitalizeSubject;
use CaptainHook\App\Hook\Message\Validator;
use CaptainHook\App\Mockery;
use SebastianFeldmann\Git\CommitMessage;
use SebastianFeldmann\Git\Repository;
use PHPUnit\Framework\TestCase;

class RulesTest extends TestCase
{
    use Mockery;

    /**
     * Tests Rulebook::execute
     */
    public function testExecuteEmptyRules(): void
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(Rules::class);
        $repo   = $this->createRepositoryMock();
        $repo->method('getCommitMsg')->willReturn(new CommitMessage('Foo bar baz'));

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests Rulebook::execute
     */
    public function testNoValidationOnMerging(): void
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(Rules::class);
        $repo   = $this->createRepositoryMock();
        $repo->expects($this->once())->method('isMerging')->willReturn(true);

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests Rulebook::execute
     */
    public function testExecuteClassNotFound(): void
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $repo   = $this->createRepositoryMock();
        $action = new Config\Action(Rules::class, [Foo::class]);

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Rulebook::execute
     */
    public function testExecuteInvalidClass(): void
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(Rules::class, [Validator::class]);
        $repo   = $this->createRepositoryMock();

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Rulebook::execute
     */
    public function testExecuteValidRule(): void
    {
        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(Rules::class, [CapitalizeSubject::class]);
        $repo   = $this->createRepositoryMock();
        $repo->method('getCommitMsg')->willReturn(new CommitMessage('Foo bar baz'));

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);

        $this->assertTrue(true);
    }

    /**
     * Tests Rule::execute
     */
    public function testNoRule(): void
    {
        $this->expectException(\Exception::class);

        $io     = new NullIO();
        $config = new Config(CH_PATH_FILES . '/captainhook.json');
        $action = new Config\Action(Rules::class, [NoRule::class]);
        $repo   = $this->createRepositoryMock();
        $repo->method('getCommitMsg')->willReturn(new CommitMessage('Foo bar baz'));

        $standard = new Rules();
        $standard->execute($config, $io, $repo, $action);
    }
}
