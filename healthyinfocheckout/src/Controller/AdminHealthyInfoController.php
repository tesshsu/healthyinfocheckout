<?php

namespace PrestaShop\Module\HealthyInfoCheckout\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminHealthyInfoController extends FrameworkBundleAdminController
{
    public $name = 'healthyinfocheckout';
    /** @var Module */
    public $module;

    public function __construct()
    {
        $this->bootstrap = true;
    }

    public function initContent(Request $request): Response
    {
        $form = $this->createForm($request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle form submission
            $content = $form->get('content')->getData();

            // Save the content or perform any necessary actions
            // ...

            return new Response('Form submitted successfully');
        }

        return $this->renderForm($form->createView());
    }

    protected function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add(
            'content',
            TextareaType::class,
            [
                'label' => 'Content',
                'required' => true,
                'attr' => [
                    'rows' => 10,
                    'cols' => 80,
                ],
            ]
        );
        $formBuilder->add('submit', SubmitType::class, ['label' => 'Save']);

        return $formBuilder->getForm();
    }

    private function renderForm(\Symfony\Component\Form\FormView $formView): Response
    {
        return $this->render(
            '@Modules/healthyinfocheckout/views/templates/admin/_partials/edit_content.html.twig',
            [
            'form' => $formView,
                'path' => 'ddddd',
        ]);
    }
}
