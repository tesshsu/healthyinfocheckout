<?php

namespace PrestaShop\Module\HealthyInfoCheckout\Controller;

use PrestaShop\Module\HealthyInfoCheckout\Entity\HealthyInfoContent;
use PrestaShop\Module\HealthyInfoCheckout\Forms\HealthyInfoContentType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AdminHealthyInfoController extends FrameworkBundleAdminController
{
    public $name = 'healthyinfocheckout';
    public $module;

    public function __construct()
    {
        $this->bootstrap = true;
    }

    const DEFAULT_LOG_FILE = 'dev2.log';
    public static function log($message, $level = 'debug', $fileName = null)
    {
        $fileDir = _PS_ROOT_DIR_ . '/var/logs/';

        if (!$fileName)
            $fileName = self::DEFAULT_LOG_FILE;

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        $formatted_message = '*' . $level . '* ' . " -- " . date('Y/m/d - H:i:s') . ': ' . $message . "\r\n";

        return file_put_contents($fileDir . $fileName, $formatted_message, FILE_APPEND);
    }

    public function initContent(Request $request): Response
    {
        $this->log('initContent');
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(HealthyInfoContentType::class);
        $form->handleRequest($request);

        // If form is submitted and valid, insert content into database
        if ($form->isSubmitted() && $form->isValid()) {
            $this->log('form submitted');
            // Get admin Id
            $admin = $this->getUser();
            $adminId = $admin->getId();
            $this->log('admin id: ', $adminId);
            $healthyInfoContent = new HealthyInfoContent();
            $healthyInfoContent->setContent($form->get('content')->getData());
            $healthyInfoContent->setAdminId($adminId);
            $healthyInfoContent->setCreatedAt(new \DateTime());
            dump($healthyInfoContent);
            $em->persist($healthyInfoContent);
            $em->flush();
        } else {
            $this->log('form not submitted');
        }

        $items = $em->getRepository(HealthyInfoContent::class)->findAll();

        return $this->render(
            '@Modules/healthyinfocheckout/views/templates/admin/_partials/edit_content.html.twig',
            [
                'form' => $form->createView(),
                'items' => $items,
            ]
        );
    }

    public function deleteContent(int $id): Response
    {
        $this->log('deleteContent');
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(HealthyInfoContent::class)->findOneBy(['id' => $id]);
        $this->log('item found: ' . print_r($item, true)); // Convert $item to string using print_r()

        if($item){
            $em->remove($item);
            $em->flush();
            $this->addFlash('success', 'Content deleted');
        }

        return $this->redirectToRoute('admin_healthyinfo_content');
    }
}
