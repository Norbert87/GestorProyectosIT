<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TipoAplicacionTraduccion
 *
 * @ORM\Table(name="tipo_aplicacion_traduccion")
 * @ORM\Entity
 */
class TipoAplicacionTraduccion
{
    
    /**
     * @var \TipoAplicacion
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="TipoAplicacion", inversedBy="traducciones")
     * @ORM\JoinColumn(name="tipo_aplicacion_id", referencedColumnName="id")
     */
    private $tipoAplicacion;

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

    public function getTipoAplicacion(): ?TipoAplicacion
    {
        return $this->tipoAplicacion;
    }

    public function setTipoAplicacion(?TipoAplicacion $tipoAplicacion): self
    {
        $this->tipoAplicacion = $tipoAplicacion;

        return $this;
    }

    
}