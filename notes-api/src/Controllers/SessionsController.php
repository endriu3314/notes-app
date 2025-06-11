<?php

namespace NotesApi\Controllers;

use NotesApi\Repositories\AccessTokenRepository;
use NotesApi\Request\Request;
use NotesApi\Request\ResponseBuilder;
use NotesApi\TemplateRenderer;

class SessionsController
{
    private readonly AccessTokenRepository $accessTokenRepository;

    public function __construct()
    {
        $this->accessTokenRepository = new AccessTokenRepository;
    }

    public function index(Request $request)
    {
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('perPage', 10);
        $offset = ($page - 1) * $perPage;

        $accessTokens = $this->accessTokenRepository->findAll($request->user()->id, $perPage, $offset);
        $total = $this->accessTokenRepository->count($request->user()->id);
        $totalPages = ceil($total / $perPage);

        return ResponseBuilder::buildViewResponse(
            TemplateRenderer::renderTemplate('sessions/index', [
                'title' => 'Sesiuni',
                'accessTokens' => $accessTokens,
                'pagination' => [
                    'currentPage' => $page,
                    'perPage' => $perPage,
                    'total' => $total,
                    'totalPages' => $totalPages,
                    'hasNextPage' => $page < $totalPages,
                ],
            ])
        );
    }
}
