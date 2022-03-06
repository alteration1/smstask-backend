<?php
namespace App\Controller;

use App\Controller\SendSmsController;
use App\Entity\Attempts;
use App\Entity\SmsText;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class VerifyPhoneController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SendSmsController
     */
    private $sendSmsService;

    public function __construct(EntityManagerInterface $entityManager, SendSmsController $sendSmsService)
    {
        $this->entityManager = $entityManager;
        $this->sendSmsService = $sendSmsService;
    }

    /**
     * @Route("/api/check/phone", name="app_check_phone", methods={"POST"})
     */
    public function checkPhone(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (!isset($content['phone']) && !$content['phone']) {
            throw new NotFoundHttpException('Phone not found.');
        }
        $phone = $content['phone'];

        $isVerified = $this->sendSmsService->isVerified($phone);

        return new JsonResponse(['check' => $isVerified], JsonResponse::HTTP_OK);

    }

    /**
     * @Route("/api/verify/phone", name="app_verify_phone", methods={"POST"})
     */
    public function verifyPhone(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (!isset($content['phone']) || !$content['phone']) {
            throw new NotFoundHttpException('Phone not found.');
        }
        $phone = $content['phone'];
        //check if phone was already verified
        $check = $this->sendSmsService->isVerified($phone);

        if ($check) {
            return new JsonResponse(['message' => "Your phone has been already validated."], JsonResponse::HTTP_OK);
        }

        if (!isset($content['code']) || !$content['code']) {
            throw new NotFoundHttpException('Code not found.');
        }
        $code = $content['code'];
        //does the code match
        $verifiedCode = $this->entityManager
            ->getRepository(SmsText::class)
            ->findOneBy(['valid' => true, 'phone' => $phone, 'code' => $code]);
        //log attempt
        $attempt = new Attempts();
        if ($verifiedCode) {
            $attempt->setCodeId($verifiedCode);
            $attempt->setSuccess(true);
            $this->entityManager->persist($attempt);
            $this->entityManager->flush();
            return new JsonResponse(['message' => "Your phone has been successfully validated."],
                JsonResponse::HTTP_OK);
        } else {
            //look for valid phone code
            $validCode = $this->entityManager
                ->getRepository(SmsText::class)
                ->findValidCode($phone);
            if ($validCode) {
                $attempt->setCodeId($validCode);
                $attempt->setSuccess(false);
                $this->entityManager->persist($attempt);
                $this->entityManager->flush();

                return new JsonResponse(['error' => "Failure! The code does not match"],
                    JsonResponse::HTTP_BAD_REQUEST);
            } else {
                //if valid code does not exist -> send one
                $code = $this->sendSmsService->generateNewCode();
                $text = 'This is your verification code ' . $code;
                //send code to phone
                $validCode = $this->sendSmsService->sendSms($phone, $text, $code);
                //log attempt
                $attempt->setCodeId($validCode);
                $attempt->setSuccess(false);
                $this->entityManager->persist($attempt);
                $this->entityManager->flush();

                return new JsonResponse(['error' => "Failure! There was no valid code registered for your phone. We sent one to your phone. Try again!"],
                    JsonResponse::HTTP_BAD_REQUEST);
            }
        }

    }

}
