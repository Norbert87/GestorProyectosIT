<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TipoAplicacion
 *
 * @ORM\Table(name="tipo_aplicacion")
 * @ORM\Entity(repositoryClass="App\Repository\TipoAplicacionRepository")
 */
class TipoAplicacion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="preciobase", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $preciobase;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false, options={"default"="1"})
     */
    private $activo = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Presupuesto", mappedBy="tipoAplicacion")
     */
    private $presupuesto;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="TipoAplicacionTraduccion", mappedBy="tipoAplicacion")
     */
    private $traducciones;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->presupuesto = new \Doctrine\Common\Collections\ArrayCollection();
        $this->traducciones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPreciobase()
    {
        return $this->preciobase;
    }

    public function setPreciobase($preciobase): self
    {
        $this->preciobase = $preciobase;

        return $this;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): self
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * @return Collection|Presupuesto[]
     */
    public function getPresupuesto(): Collection
    {
        return $this->presupuesto;
    }

    public function addPresupuesto(Presupuesto $presupuesto): self
    {
        if (!$this->presupuesto->contains($presupuesto)) {
            $this->presupuesto[] = $presupuesto;
            $presupuesto->addTipoAplicacion($this);
        }

        return $this;
    }

    public function removePresupuesto(Presupuesto $presupuesto): self
    {
        if ($this->presupuesto->contains($presupuesto)) {
            $this->presupuesto->removeElement($presupuesto);
            $presupuesto->removeTipoAplicacion($this);
        }

        return $this;
    }

    /**
     * @return Collection|TipoAplicacionTraduccion[]
     */
    public function getTraducciones(): Collection
    {
        return $this->traducciones;
    }

    public function addTraduccione(TipoAplicacionTraduccion $traduccione): self
    {
        if (!$this->traducciones->contains($traduccione)) {
            $this->traducciones[] = $traduccione;
            $traduccione->setTipoAplicacion($this);
        }

        return $this;
    }

    public function removeTraduccione(TipoAplicacionTraduccion $traduccione): self
    {
        if ($this->traducciones->contains($traduccione)) {
            $this->traducciones->removeElement($traduccione);
            // set the owning side to null (unless already changed)
            if ($traduccione->getTipoAplicacion() === $this) {
                $traduccione->setTipoAplicacion(null);
            }
        }

        return $this;
    }

}
