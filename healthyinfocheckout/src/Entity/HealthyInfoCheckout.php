<?php
namespace PrestaShop\Module\HealthyInfoCheckout\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class HealthyInfoCheckout
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id_healthy_info_checkout", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * Identifier of customer
     *
     * @var int
     * @ORM\Column(name="id_customer", type="integer")
     */
    public $id_customer;

    /**
     * boolean of has insurance
     *
     * @var boolean
     * @ORM\Column(name="has_insurance", type="boolean")
     */
    public $has_insurance;

    /**
     * boolean of has prescription
     *
     * @var boolean
     * @ORM\Column(name="has_prescription", type="boolean")
     */
    public $has_prescription;

    /**
     * HTML format of Extra note
     *
     * @var array
     * @ORM\Column(name="extra_note", type="text")
     */
    public $extra_note;

    /** @var string Object creation date
     * @ORM\Column(name="created_at", type="datetime") */
    public $created_at;

    public function getId()
    {
        return $this->id;
    }

    public function getIdCustomer()
    {
        return $this->id_customer;
    }

    public function getHasInsurance()
    {
        return $this->has_insurance;
    }

    public function getHasPrescription()
    {
        return $this->has_prescription;
    }

    public function getExtraNote()
    {
        return $this->extra_note;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setIdCustomer($id_customer)
    {
        $this->id_customer = $id_customer;
    }

    public function setHasInsurance($has_insurance)
    {
        $this->has_insurance = $has_insurance;
    }

    public function setHasPrescription($has_prescription)
    {
        $this->has_prescription = $has_prescription;
    }

    public function setExtraNote($extra_note)
    {
        $this->extra_note = $extra_note;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'id_customer' => $this->id_customer,
            'has_insurance' => $this->has_insurance,
            'has_prescription' => $this->has_prescription,
            'extra_note' => $this->extra_note,
            'created_at' => $this->created_at,
        ];
    }
}
