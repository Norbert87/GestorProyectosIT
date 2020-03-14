<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CaracteristicaTraduccion
 *
 * @ORM\Table(name="caracteristica_traduccion")
 * @ORM\Entity
 */
class CaracteristicaTraduccion
{

    /**
     * @var \TipoAplicacion
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Caracteristica", inversedBy="traducciones")
     * @ORM\JoinColumn(name="caracteristica_id", referencedColumnName="id")
     */
    private $caracteristica;

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="locale", type="string", length=4, nullable=false)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCaracteristica(): ?Caracteristica
    {
        return $this->caracteristica;
    }

    public function setCaracteristica(?Caracteristica $caracteristica): self
    {
        $this->caracteristica = $caracteristica;

        return $this;
    }
}