<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
namespace PrestaShop\Module\HealthyInfoCheckout\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class HealthyInfoContent
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id_healthy_info", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;
    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    public $content;
    /**
     * Date of creation
     *
     * @var string
     * @ORM\Column(name="created_at", type="datetime")
     */
    public $created_at;
    /**
     * Identifier of admin update
     *
     * @var int
     * @ORM\Column(name="updated_by", type="integer")
     */
    public $updated_by;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return healthyInfoContent
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
    public function getAdminId()
    {
        return $this->updated_by;
    }
    /**
     * @param int $id
     *
     * @return healthyInfoContent
     */
    public function setAdminId($id)
    {
        $this->updated_by = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function setCreatedAt($date)
    {
        $this->created_at = $date;
        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'content' => $this->getContent(),
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
        ];
    }
}
