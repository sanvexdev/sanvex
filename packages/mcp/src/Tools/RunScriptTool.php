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
        // Disabled by default. Enable only in explicitly trusted environments via config.
        if (!config('sanvex.mcp.allow_run_script', false)) {
            return [
                'error' => 'RunScriptTool is disabled. Set sanvex.mcp.allow_run_script=true to enable (trusted environments only).',
            ];
        }

        $script = $params['script'] ?? null;

        if (!$script) {
            return ['error' => 'script parameter is required'];
        }

        // String-literal blocklist cannot fully prevent eval abuse. This tool relies on the
        // operator trusting their environment (allow_run_script=true) and is intentionally
        // kept simple. Deployers should not expose this tool to untrusted agents.
        //
        // Block common direct shell-execution calls as a first-line defence.
        $forbidden = [
            'system', 'exec', 'passthru', 'shell_exec', 'popen', 'proc_open',
            'file_put_contents', 'file_get_contents', 'unlink', 'rename',
            'eval', 'assert', 'create_function', 'call_user_func', 'call_user_func_array',
        ];
        $lowScript = strtolower($script);
        foreach ($forbidden as $fn) {
            // Match bare name followed by any amount of whitespace/( to catch `system (` too
            if (preg_match('/\b' . preg_quote($fn, '/') . '\s*\(/i', $script)) {
                return ['error' => "Forbidden function [{$fn}] is not allowed in scripts."];
            }
        }

        $connector = $this->connector;

        try {
            // phpcs:ignore Squiz.PHP.Eval.Discouraged
            $result = eval("return {$script};");
            return ['result' => $result];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
