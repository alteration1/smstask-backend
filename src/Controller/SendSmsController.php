<?php
namespace App\Controller;

use App\Entity\Attempts;
use App\Entity\SmsText;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Rossana Pencheva <rossana.ruseva@gmail.com>
 */
class SendSmsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/api/send/code", name="app_send_code", methods={"POST"})
     */
    public function requestSendCode(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['phone']) || !$content['phone']) {
            throw new NotFoundHttpException('Phone not found.');
        }
        $phone = $content['phone'];

        //check if phone was already verified
        $isVerified = $this->isVerified($phone);
        if ($isVerified) {
            return new JsonResponse(['check' => $isVerified], JsonResponse::HTTP_OK);
        }

        //find valid code from less than one minute
        $validCodeFromOneMinute = $this->entityManager
            ->getRepository(SmsText::class)
            ->findValidFromOneMinuteAgo($phone);
        if ($validCodeFromOneMinute) {
            return new JsonResponse(['warning' => "А code has already been sent to your phone."],
                JsonResponse::HTTP_OK);
        } else {
            //if old codes are still valid make then unvalid
            $validCodes = $this->entityManager
                ->getRepository(SmsText::class)
                ->findBy(['valid' => true, 'phone' => $phone]);
            foreach ($validCodes as $validCode) {
                $validCode->setValid(false);
                $this->entityManager->persist($validCode);
                $this->entityManager->flush();
            }
            $code = $this->generateNewCode();
            $text = 'This is your verification code ' . $code;
            //send code to phone
            $this->sendSms($phone, $text, $code);
        }

        return new JsonResponse(['message' => "We've sent a verification code to your phone."],
            JsonResponse::HTTP_OK);
    }

    public function sendSms(string $phone, string $text, string $code = ''): SmsText
    {
        $smsSended = new SmsText($phone, $text, $code);
        $this->entityManager->persist($smsSended);
        $this->entityManager->flush();
        return $smsSended;
    }

    protected function generateNewCode(): int
    {
        $randomNumber = mt_rand(100000, 999999);
        return $randomNumber;
    }

    public function isVerified(string $phone): bool
    {
        $isVerified = $this->entityManager
            ->getRepository(Attempts::class)
            ->isPhoneVerified($phone);
        if ($isVerified) {
            return true;
        } else {
            return false;
        }
    }

}
