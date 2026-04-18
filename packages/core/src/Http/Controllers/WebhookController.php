<?php

namespace Sanvex\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Sanvex\Core\SanvexManager;

class WebhookController extends Controller
{
    public function __construct(private readonly SanvexManager $connector) {}

    public function handle(Request $request): JsonResponse
    {
        $headers = $request->headers->all();
        $payload = $request->all();

        $result = $this->connector->processWebhook($headers, $payload);

        return response()->json(
            $result->response ?? ['status' => $result->success ? 'ok' : 'error', 'error' => $result->error],
            $result->status
        );
    }
}
