<?php

/**
 * GoldController.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GoldServiceInterface;

/**
 * Class GoldController.
 */
class GoldController extends AbstractController
{
    /**
     * Index action.
     * It uses a local json file and decodes it to an array. Then it checks if request data in it is correct.
     * After that it makes external GET request with Http client to the http://api.nbp.pl in order to get gold prices as a json response.
     * After it receives a json response, it's being decoded and average gold prices are calculates.
     * After everything is done action return response in json format.
     *
     * @param GoldServiceInterface $goldService Gold service interface
     *
     * @return JsonResponse HTTP Json Response
     */
    #[Route('/api/gold', methods: ['GET'])]
    public function index(GoldServiceInterface $goldService): JsonResponse
    {
        $file = file_get_contents(dirname(__DIR__).'/Files/data.json');
        $decoded = json_decode($file, true);

        $startDate = substr($decoded['from'], 0, 10);
        $endDate = substr($decoded['to'], 0, 10);

        if (!$goldService->checkIfCorrectFormat($decoded['from'], 'Y-m-d\TH:i:sP') || !$goldService->checkIfCorrectDate($decoded['from']) || !$goldService->checkIfCorrectFormat($decoded['to'], 'Y-m-d\TH:i:sP') || !$goldService->checkIfCorrectDate($decoded['to'])) {
            throw new BadRequestHttpException('Bad request data!');
        }

        $url = $this->getParameter('app.npb_url').'/'.$startDate.'/'.$endDate.'/?format=json';

        $response = $goldService->makeExternalRequest($url);

        return $this->json([
                'from' => date(DATE_ISO8601, strtotime($response[0]['data'])),
                'to' => date(DATE_ISO8601, strtotime(end($response)['data'])),
                'avg' => $goldService->calculateAvg($response),
        ]);
    }
}
