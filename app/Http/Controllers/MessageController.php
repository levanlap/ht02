<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepository;
use Illuminate\Http\Request;
use App\Transformers\MessageTransformer;

class MessageController extends Controller
{
    /**
     * Instance of MessageRepository.
     *
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * Instanceof MessageTransformer.
     *
     * @var MessageTransformer
     */
    private $messageTransformer;

    /**
     * Constructor.
     *
     * @param MessageRepository $messageRepository
     * @param MessageTransformer $messageTransformer
     */
    public function __construct(MessageRepository $messageRepository, MessageTransformer $messageTransformer)
    {
        $this->messageRepository = $messageRepository;
        $this->messageTransformer = $messageTransformer;

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $messages = $this->messageRepository->findBy($request->all());

        return $this->respondWithCollection($messages, $this->messageTransformer);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function show($id)
    {
        $message = $this->messageRepository->findOne($id);

        if (!$message instanceof Message) {
            return $this->sendNotFoundResponse("The message with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('show', $message);

        return $this->respondWithItem($message, $this->messageTransformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function store(Request $request)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->storeRequestValidationRules($request));

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $message = $this->messageRepository->save($request->all());

        if (!$message instanceof Message) {
            return $this->sendCustomResponse(500, 'Error occurred on creating Message');
        }

        return $this->setStatusCode(201)->respondWithItem($message, $this->messageTransformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validatorResponse = $this->validateRequest($request, $this->updateRequestValidationRules($request));

        // Send failed response if validation fails
        if ($validatorResponse !== true) {
            return $this->sendInvalidFieldResponse($validatorResponse);
        }

        $message = $this->messageRepository->findOne($id);

        if (!$message instanceof Message) {
            return $this->sendNotFoundResponse("The message with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('update', $message);


        $message = $this->messageRepository->update($message, $request->all());

        return $this->respondWithItem($message, $this->messageTransformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function destroy($id)
    {
        $message = $this->messageRepository->findOne($id);

        if (!$message instanceof Message) {
            return $this->sendNotFoundResponse("The message with id {$id} doesn't exist");
        }

        // Authorization
        $this->authorize('destroy', $message);

        $this->messageRepository->delete($message);

        return response()->json(null, 204);
    }

    /**
     * Store Request Validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function storeRequestValidationRules(Request $request)
    {
       return [
           'userId'     => 'required|exists:users,id',
           'subject'    => 'required',
           'message'    => 'required',
        ];
    }

    /**
     * Update Request validation Rules
     *
     * @param Request $request
     * @return array
     */
    private function updateRequestValidationRules(Request $request)
    {
        return [
            'subject'    => '',
            'message'    => '',
        ];
    }
}