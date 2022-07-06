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
     * @var GoldServiceInterface Gold service interface
     */
    private GoldServiceInterface $goldService;

    /**
     * Constructor.
     *
     * @param GoldServiceInterface $goldService Gold service interface
     */
    public function __construct(GoldServiceInterface $goldService)
    {
        $this->goldService = $goldService;
    }

    /**
     * Index action.
     * It uses a local json file and decodes it to an array. Then it checks if request data in it is correct.
     * After that it makes external GET request to the http://api.nbp.pl in order to get gold prices as a json response.
     * After it receives a json response, it's being decoded and average gold prices are calculates.
     * After everything is done action return response in json format.
     *
     * @return JsonResponse HTTP Json Response
     */
    #[Route('/api/gold', methods: ['POST'])]
    public function index(): JsonResponse
    {
        $file = file_get_contents(dirname(__DIR__).'/Files/data.json');
        $decoded = json_decode($file, true);

        $startDate = substr($decoded['from'], 0, 10);
        $endDate = substr($decoded['to'], 0, 10);

        if (!$this->goldService->checkIfCorrectFormat($decoded['from'], 'Y-m-d\TH:i:sP') || !$this->goldService->checkIfCorrectDate($decoded['from']) || !$this->goldService->checkIfCorrectFormat($decoded['to'], 'Y-m-d\TH:i:sP') || !$this->goldService->checkIfCorrectDate($decoded['to'])) {
            throw new BadRequestHttpException('Bad request data!');
        } else {
            $url = 'https://api.nbp.pl/api/cenyzlota/'.$startDate.'/'.$endDate.'/?format=json';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            $responseDecoded = json_decode($response, true);

            return $this->json([
                'from' => date(DATE_ISO8601, strtotime($responseDecoded[0]['data'])),
                'to' => date(DATE_ISO8601, strtotime(end($responseDecoded)['data'])),
                'avg' => $this->goldService->calculateAvg($responseDecoded),
            ]);
        }
    }
}
