<?php

namespace Sanvex\Mcp\Tools;

use Sanvex\Core\ConnectorManager;

class RunScriptTool
{
    public string $name = 'sanvex_run_script';
    public string $description = 'Execute a PHP expression with $connector in scope (opt-in, trusted environments only)';

    public function __construct(private readonly ConnectorManager $connector) {}

    public function run(array $params): array
    {
        // Disabled by default. Enable only in trusted environments via config.
        if (!config('sanvex.mcp.allow_run_script', false)) {
            return [
                'error' => 'RunScriptTool is disabled. Set sanvex.mcp.allow_run_script=true to enable (trusted environments only).',
            ];
        }

        $script = $params['script'] ?? null;

        if (!$script) {
            return ['error' => 'script parameter is required'];
        }

        // Block known dangerous functions before eval
        $forbidden = ['system(', 'exec(', 'passthru(', 'shell_exec(', 'popen(', 'proc_open(', 'file_put_contents(', 'unlink('];
        foreach ($forbidden as $pattern) {
            if (str_contains($script, $pattern)) {
                return ['error' => "Forbidden function [{$pattern}] detected in script."];
            }
        }

        $connector = $this->connector;

        try {
            $result = eval("return {$script};");
            return ['result' => $result];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
