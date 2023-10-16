<?php

namespace App\Controller;

use App\Service\UxPackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormError;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Cropperjs\Factory\CropperInterface;
use Symfony\UX\Cropperjs\Form\CropperType;

class CropperjsController extends AbstractController
{

    public function __construct(
            private Packages $assets,
            ParameterBagInterface $params,
    )
    {
        $this->projectDir = $params->get('kernel.project_dir');
    }

    #[Route('/cropperjs', name: 'app_cropperjs')]
    public function __invoke(CropperInterface $cropper, Request $request): Response
    {
        //$package = $packageRepository->find('cropperjs');

        $crop = $cropper->createCrop($this->projectDir . '/assets/img/large.jpg');
        $crop->setCroppedMaxSize(1000, 750);

        $form = $this->createFormBuilder(['crop' => $crop])
                ->add('crop', CropperType::class, [
                    'public_url' => $this->assets->getUrl('/public/img/large.jpg'),
                    'cropper_options' => [
                        'aspectRatio' => 4 / 3,
                        'preview' => '#cropper-preview',
                        'scalable' => false,
                        'zoomable' => false,
                    ],
                ])
                ->getForm();

        $form->handleRequest($request);
        $croppedImage = null;
        $croppedThumbnail = null;
        if ($form->isSubmitted()) {
            $form->addError(new FormError('🤩'));
            $croppedImage = sprintf('data:image/jpeg;base64,%s', base64_encode($crop->getCroppedImage()));
            $croppedThumbnail = sprintf('data:image/jpeg;base64,%s', base64_encode($crop->getCroppedThumbnail(100, 75)));
        }

        return $this->render('cropperjs/index.html.twig', [
                    //'package' => $package,
                    'form' => $form,
                    'croppedImage' => $croppedImage,
                    'croppedThumbnail' => $croppedThumbnail,
        ]);
    }
}
