<?php

namespace NotesApi\Controllers\Api;

use NotesApi\Repositories\NoteRepository;
use NotesApi\Repositories\UserRepository;
use NotesApi\Request\InputSanitizer;
use NotesApi\Request\Request;
use NotesApi\Request\ResponseBuilder;

class NotesController
{
    private readonly NoteRepository $noteRepository;

    private readonly UserRepository $userRepository;

    public function __construct()
    {
        $this->noteRepository = new NoteRepository;
        $this->userRepository = new UserRepository;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $onlyPersonal = filter_var($request->get('onlyPersonal', false), FILTER_VALIDATE_BOOL);
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('perPage', 10);
        $offset = ($page - 1) * $perPage;

        $notes = $this->noteRepository->findAll($user->id, ! $onlyPersonal, $perPage, $offset);
        $total = $this->noteRepository->count($user->id, ! $onlyPersonal);
        $totalPages = ceil($total / $perPage);

        return ResponseBuilder::buildJsonResponse([
            'message' => 'Notes fetched successfully',
            'data' => $notes,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => $totalPages,
                'hasNextPage' => $page < $totalPages,
                'hasPreviousPage' => $page > 1,
            ],
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = $request->user();
        if (! $user) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $note = $this->noteRepository->findById($id);

        if (! $note) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Note not found'], 404);
        }

        if (! $this->noteRepository->checkAuthorization($id, $user->id)) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        return ResponseBuilder::buildJsonResponse(['message' => 'Note fetched successfully', 'data' => $note]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $title = $request->input('title', 'Untitled');
        $content = $request->input('content', '');

        if (! $title) {
            return ResponseBuilder::buildJsonResponse(['error' => 'Title is required'], 422);
        }

        $title = InputSanitizer::sanitize($title);
        $content = InputSanitizer::sanitize($content);

        $note = $this->noteRepository->create($user->id, $title, $content);

        return ResponseBuilder::buildJsonResponse(['message' => 'Note created successfully', 'data' => $note]);
    }

    public function update(Request $request, int $id)
    {
        $user = $request->user();
        if (! $user) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $note = $this->noteRepository->findById($id);
        if (! $note) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Note not found'], 404);
        }

        if (! $this->noteRepository->checkAuthorization($id, $user->id)) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $title = $request->input('title', $note->title);
        $content = $request->input('content', $note->content);

        if (! $title && ! $content) {
            return ResponseBuilder::buildJsonResponse(['error' => 'Title or content is required'], 422);
        }

        $title = InputSanitizer::sanitize($title);
        $content = InputSanitizer::sanitize($content);

        $note = $this->noteRepository->update($id, $title, $content);

        return ResponseBuilder::buildJsonResponse(['message' => 'Note updated successfully', 'data' => $note]);
    }

    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        if (! $user) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $note = $this->noteRepository->findById($id);
        if (! $note) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Note not found'], 404);
        }

        if ($note->userId !== $user->id) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $this->noteRepository->delete($id);

        return ResponseBuilder::buildJsonResponse(['message' => 'Note deleted successfully']);
    }

    public function authorize(Request $request, int $id)
    {
        $user = $request->user();
        if (! $user) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $note = $this->noteRepository->findById($id);
        if (! $note) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Note not found'], 404);
        }

        if ($note->userId !== $user->id) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $userEmailToAuthorize = $request->input('email');
        if (! $userEmailToAuthorize) {
            return ResponseBuilder::buildJsonResponse(['error' => 'Email is required'], 422);
        }

        $userToAuthorize = $this->userRepository->findByEmail($userEmailToAuthorize);
        if (! $userToAuthorize) {
            return ResponseBuilder::buildJsonResponse(['error' => 'User not found'], 404);
        }

        $this->noteRepository->authorize($id, $userToAuthorize->id);

        return ResponseBuilder::buildJsonResponse(['message' => 'User authorized to note successfully']);
    }

    public function unauthorize(Request $request, int $id, int $userId)
    {
        $user = $request->user();
        if (! $user) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $note = $this->noteRepository->findById($id);
        if (! $note) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Note not found'], 404);
        }

        if ($note->userId !== $user->id) {
            return ResponseBuilder::buildJsonResponse(['message' => 'Unauthorized'], 401);
        }

        $this->noteRepository->unauthorize($id, $userId);

        return ResponseBuilder::buildJsonResponse(['message' => 'User unauthorized from note successfully']);
    }
}
