<?php

namespace PrestaShop\Module\HealthyInfoCheckout\Controller;

use PrestaShop\Module\HealthyInfoCheckout\Entity\Customer;
use PrestaShop\Module\HealthyInfoCheckout\Entity\HealthyInfoContent;
use PrestaShop\Module\HealthyInfoCheckout\Entity\HealthyInfoCheckout;
use PrestaShop\Module\HealthyInfoCheckout\Forms\HealthyInfoContentType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AdminHealthyInfoController extends FrameworkBundleAdminController
{
    public $name = 'healthyinfocheckout';
    public $module;
    protected $context;

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
                'partial' => 'home',
                'home_url' => $this->generateUrl('admin_healthyinfo_content'),
                'list_url' => $this->generateUrl('admin_healthinfo_list'),
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

    public function listContent(): Response
    {
        $this->log('listContent');
        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository(HealthyInfoCheckout::class)->findBy([], ['created_at' => 'DESC']);

        $customerRepository = $em->getRepository(Customer::class);
        $formattedItems =[];

        foreach($items as $item){
            $customerId = $item->id_customer;
            $customer = $customerRepository->find($customerId);

            $formattedItems[] = [
                'id' => $item->getId(),
                'customer' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                'email' => $customer->getEmail(),
                'has_insurance' => $item->has_insurance,
                'has_prescription' => $item->has_prescription,
                'extra_note' => $item->extra_note,
                'created_at' => $item->created_at,
            ];
        }

        return $this->render(
            '@Modules/healthyinfocheckout/views/templates/admin/_partials/list.html.twig',
            [
                'items' => $formattedItems,
                'home_url' => $this->generateUrl('admin_healthyinfo_content'),
                'list_url' => $this->generateUrl('admin_healthinfo_list'),
            ]
        );
    }

    public function updateHealthyInfoList(Request $request, $id): Response
    {
        $this->log('updateHealthyInfoList');
        $em = $this->getDoctrine()->getManager();
        $item = $em->getRepository(HealthyInfoCheckout::class)->findOneBy(['id' => $id]);

        if(!$item){
            $this->addFlash('error', 'Item not found');
            return $this->redirectToRoute('admin_healthinfo_list');
        }

        $formData = $request->request->get('update_form');

        // Update the item with the form data
        $item->setHasInsurance($formData['has_insurance'] ?? false);
        $item->setHasPrescription($formData['has_prescription'] ?? false);
        $item->setExtraNote($formData['extra_note'] ?? '');
        // Persist and flush the changes to the database
        $em->persist($item);
        $em->flush();

        $this->addFlash('success', 'Item updated');

        return $this->redirectToRoute('admin_healthinfo_list');
    }


    public function showUpdateForm(int $id): Response
    {
        // Get the item based on the provided ID
        $item = $this->getDoctrine()->getRepository(HealthyInfoCheckout::class)->find($id);

        // Render the update form template and pass the item to it
        return $this->render('@Modules/healthyinfocheckout/views/templates/admin/_partials/update_form.html.twig', [
            'item' => $item,
            'home_url' => $this->generateUrl('admin_healthyinfo_content'),
            'list_url' => $this->generateUrl('admin_healthinfo_list'),
        ]);
    }
}
