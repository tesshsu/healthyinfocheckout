<?php
namespace PrestaShop\Module\HealthyInfoCheckout\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class HealthyInfoContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class , array(
                "attr" => array(
                    "placeholder" => "The id_healthy_info",
                )
            ))
            ->add('content', TextType::class, [
                'label' => 'Content',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Content',
                ],
            ])
            ->add('create_at', HiddenType::class , array(
                "attr" => array(
                    "placeholder" => "create_at",
                )
            ))
            ->add('updated_by', HiddenType::class , array(
                "attr" => array(
                    "placeholder" => "updated_by",
                )
            ))
            ->add('save', SubmitType::class, [
                'label' => 'Save',
            ]);
    }
}
