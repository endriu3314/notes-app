<?php

namespace NotesApi\Controllers;

use NotesApi\Repositories\NoteRepository;
use NotesApi\Repositories\UserRepository;
use NotesApi\Request\InputSanitizer;
use NotesApi\Request\Request;
use NotesApi\Request\ResponseBuilder;
use NotesApi\TemplateRenderer;

// TODO: Add authorizatrion to paths
// TODO: Add authorization to the note
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
        $onlyPersonal = filter_var($request->get('onlyPersonal', false), FILTER_VALIDATE_BOOL);
        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('perPage', 10);
        $offset = ($page - 1) * $perPage;

        $notes = $this->noteRepository->findAll($request->user()->id, ! $onlyPersonal, $perPage, $offset);
        $total = $this->noteRepository->count($request->user()->id, ! $onlyPersonal);
        $totalPages = ceil($total / $perPage);

        return ResponseBuilder::buildViewResponse(
            TemplateRenderer::renderTemplate('notes/index', [
                'title' => 'Notite',
                'notes' => $notes,
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

    public function show(Request $request, int|string $id)
    {
        $note = $this->noteRepository->findById((int) $id);
        if (! $note) {
            return $request->with('error', 'Notita nu exista')->redirect('/app/notes');
        }

        if (! $this->noteRepository->checkAuthorization($id, $request->user()->id)) {
            return $request->with('error', 'Nu ai access la aceasta notita')->redirect('/app/notes');
        }

        return ResponseBuilder::buildViewResponse(
            TemplateRenderer::renderTemplate('notes/show', [
                'title' => 'Notita',
                'note' => $note,
                'errors' => $request->getFlash('errors', []),
            ])
        );
    }

    public function create(Request $request)
    {
        return ResponseBuilder::buildViewResponse(
            TemplateRenderer::renderTemplate('notes/create', [
                'title' => 'Creare notita',
                'errors' => $request->getFlash('errors', []),
            ])
        );
    }

    public function store(Request $request)
    {
        $title = $request->input('title');
        $content = $request->input('content');

        if (! $title) {
            return $request
                ->withErrors(['title' => 'Titlu este obligatoriu'])
                ->with('title', $title)
                ->with('content', $content)
                ->redirectBack();
        }

        $title = InputSanitizer::sanitize($title);
        $content = InputSanitizer::sanitize($content);

        $note = $this->noteRepository->create($request->user()->id, $title, $content);

        return $request->redirect('/app/notes/'.$note->id);
    }

    public function update(Request $request, int|string $id)
    {
        $user = $request->user();

        $note = $this->noteRepository->findById((int) $id);
        if (! $note) {
            return $request->with('error', 'Notita nu exista')->redirect('/app/notes');
        }

        if (! $this->noteRepository->checkAuthorization($id, $user->id)) {
            return $request->with('error', 'Nu ai access sa modifici aceasta notita')->redirect('/app/notes');
        }

        $title = $request->input('title');
        $content = $request->input('content');

        if (! $title) {
            return $request
                ->withErrors(['title' => 'Titlu este obligatoriu'])
                ->with('title', $title)
                ->with('content', $content)
                ->redirectBack();
        }

        if (! $content) {
            return $request
                ->withErrors(['content' => 'Continutul este obligatoriu'])
                ->with('title', $title)
                ->with('content', $content)
                ->redirectBack();
        }

        $title = InputSanitizer::sanitize($title);
        $content = InputSanitizer::sanitize($content);

        $note = $this->noteRepository->update($note->id, $title, $content);

        return $request
            ->with('success', 'Notita actualizata cu succes')
            ->redirect('/app/notes/'.$note->id);
    }

    public function destroy(Request $request, int|string $id)
    {
        $note = $this->noteRepository->findById((int) $id);
        if (! $note) {
            return $request
                ->with('error', 'Notita nu a fost gasita')
                ->withErrors(['delete' => 'Notita nu a fost gasita'])
                ->redirectBack();
        }

        if ($note->user->id !== $request->user()->id) {
            return $request
                ->with('error', 'Nu ai permisiuni sa stergi aceasta notita')
                ->withErrors(['delete' => 'Nu ai permisiuni sa stergi aceasta notita'])
                ->redirectBack();
        }

        $this->noteRepository->delete($note->id);

        return $request->with('success', 'Notita stearsa cu succes')->redirect('/app/notes');
    }

    public function authorize(Request $request, int $id)
    {
        $user = $request->user();

        $note = $this->noteRepository->findById($id);
        if (! $note) {
            return $request->withErrors(['authorize' => 'Notita nu a fost gasita'])->redirectBack();
        }

        if ($note->userId !== $user->id) {
            return $request->withErrors(['authorize' => 'Nu ai permisiuni sa autorizezi utilizatorul'])->redirectBack();
        }

        $userEmailToAuthorize = $request->input('email');
        if (! $userEmailToAuthorize) {
            return $request->withErrors(['authorize' => 'Email este obligatoriu'])->redirectBack();
        }

        $userToAuthorize = $this->userRepository->findByEmail($userEmailToAuthorize);
        if (! $userToAuthorize) {
            return $request->withErrors(['authorize' => 'Utilizatorul nu a fost gasit'])->redirectBack();
        }

        $this->noteRepository->authorize($id, $userToAuthorize->id);

        return $request->with('success', 'Utilizator autorizat cu succes')->redirectBack();
    }

    public function unauthorize(Request $request, int|string $id, int|string $userId)
    {
        $user = $request->user();

        $note = $this->noteRepository->findById($id);
        if (! $note) {
            return $request->withErrors(['unauthorize' => 'Notita nu a fost gasita'])->redirectBack();
        }

        if ($note->userId !== $user->id) {
            return $request->withErrors(['unauthorize' => 'Nu ai permisiuni sa dezautorizezi utilizatorul'])->redirectBack();
        }

        $this->noteRepository->unauthorize($id, $userId);

        return $request->with('success', 'Utilizator dezautorizat cu succes')->redirectBack();
    }
}
