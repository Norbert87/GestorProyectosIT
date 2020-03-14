<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Caracteristica
 *
 * @ORM\Table(name="caracteristica", indexes={@ORM\Index(name="IDX_9AB87F8732AD2", columns={"tipo_aplicacion_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\CaracteristicaRepository")
 */
class Caracteristica
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
     * @var \TipoAplicacion
     *
     * @ORM\ManyToOne(targetEntity="TipoAplicacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_aplicacion_id", referencedColumnName="id")
     * })
     */
    private $tipoAplicacion;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Presupuesto", inversedBy="caracteristica")
     * @ORM\JoinTable(name="presupuesto_caracteristicas",
     *   joinColumns={
     *     @ORM\JoinColumn(name="caracteristica_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="presupuesto_id", referencedColumnName="id")
     *   }
     * )
     */
    private $presupuesto;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="CaracteristicaTraduccion", mappedBy="caracteristica")
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

    public function getTipoAplicacion(): ?TipoAplicacion
    {
        return $this->tipoAplicacion;
    }

    public function setTipoAplicacion(?TipoAplicacion $tipoAplicacion): self
    {
        $this->tipoAplicacion = $tipoAplicacion;

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
        }

        return $this;
    }

    public function removePresupuesto(Presupuesto $presupuesto): self
    {
        if ($this->presupuesto->contains($presupuesto)) {
            $this->presupuesto->removeElement($presupuesto);
        }

        return $this;
    }

    /**
     * @return Collection|CaracteristicaTraduccion[]
     */
    public function getTraducciones(): Collection
    {
        return $this->traducciones;
    }

    public function addTraduccione(CaracteristicaTraduccion $traduccione): self
    {
        if (!$this->traducciones->contains($traduccione)) {
            $this->traducciones[] = $traduccione;
            $traduccione->setCaracteristica($this);
        }

        return $this;
    }

    public function removeTraduccione(CaracteristicaTraduccion $traduccione): self
    {
        if ($this->traducciones->contains($traduccione)) {
            $this->traducciones->removeElement($traduccione);
            // set the owning side to null (unless already changed)
            if ($traduccione->getCaracteristica() === $this) {
                $traduccione->setCaracteristica(null);
            }
        }

        return $this;
    }

}
