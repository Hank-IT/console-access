<?php

namespace HankIT\ConsoleAccess;

use HankIT\ConsoleAccess\Exceptions\MissingCommandException;
use HankIT\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

class ConsoleAccess
{
    protected AdapterInterface $adapter;

    protected ?string $sudo = null;

    protected ?Closure $pre = null;

    protected ?Closure $post = null;

    protected array $params = [];

    protected ?string $bin = null;

    protected int $start;

    protected int $end;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function sudo(string $sudo = '/usr/bin/sudo')
    {
        $this->sudo = $sudo;

        return $this;
    }

    public function bin(string $bin, bool $escape = true)
    {
        if ($escape) {
            // prevent multiple commands from
            // being passed by escaping the value
            $bin = escapeshellcmd($bin);
        }

        $this->bin = $bin;

        return $this;
    }

    public function param(string $param, bool $escape = true)
    {
        if ($escape) {
            // prevent multiple parameters from being
            // passed by escaping the value
            $param = escapeshellarg($param);
        }

        $this->params[] = [
            'param' => $param,
            'hidden' => false,
        ];

        return $this;
    }

    public function hiddenParam(string $param, bool $escape = true)
    {
        if ($escape) {
            // prevent multiple parameters from being
            // passed by escaping the value
            $param = escapeshellarg($param);
        }

        $this->params[] = [
            'param' => $param,
            'hidden' => true,
        ];

        return $this;
    }

    public function exec(Closure $live = null)
    {
        if (is_null($this->bin)) {
            throw new MissingCommandException('Command is missing');
        }

        if (! is_null($this->pre)) {
            call_user_func($this->pre, $this->getCommand());
        }

        $this->start = time();

        $this->adapter->run($this->buildCommand(), $live);

        $this->end = time();

        if (! is_null($this->post)) {
            call_user_func_array($this->post, [$this->getCommand(), $this->getExitStatus(), $this->start, $this->end, $this->getDuration()]);
        }

        $this->params = [];
    }

    public function getOutput(): ?string
    {
        return $this->adapter->getOutput();
    }

    public function getExitStatus(): int
    {
        return $this->adapter->getExitStatus();
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function setPreExec(Closure $function): self
    {
        $this->pre = $function;

        return $this;
    }

    public function setPostExec(Closure $function): self
    {
        $this->post = $function;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function getDuration(): int
    {
        return $this->end - $this->start;
    }

    public function getCommand(): string
    {
        return $this->buildCommandWithoutHiddenParams();
    }

    protected function buildCommand(): string
    {
        // Prepend sudo if enabled
        $command = $this->sudo
            ?  $this->sudo . ' ' . $this->bin
            :  $this->bin;

        foreach ($this->params as $param) {
            $command .= ' ' . $param['param'];
        }

        return $command;
    }

    protected function buildCommandWithoutHiddenParams(): string
    {
        $command = $this->sudo
            ? $this->sudo . ' ' . $this->bin
            : $this->bin;

        foreach ($this->params as $param) {
            $command .= $param['hidden']
                ? ' hidden'
                : ' ' . $param['param'];
        }

        return $command;
    }
}
