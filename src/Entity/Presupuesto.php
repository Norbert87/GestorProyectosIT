<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Presupuesto
 *
 * @ORM\Table(name="presupuesto")
 * @ORM\Entity
 */
class Presupuesto
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_solicitud", type="date", nullable=true)
     */
    private $fechaSolicitud;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_entrega", type="date", nullable=true)
     */
    private $fechaEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=false)
     */
    private $telefono;

    /**
     * @var string|null
     *
     * @ORM\Column(name="otras", type="text", nullable=true)
     */
    private $otras;

    /**
     * @var float|null
     *
     * @ORM\Column(name="preciofinal", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $preciofinal;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false, options={"default"="1"})
     */
    private $activo = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="locale", type="string", length=4, nullable=true)
     */
    private $locale;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estado", type="string", length=255, nullable=true)
     */
    private $estado = 'pendiente';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Caracteristica", mappedBy="presupuesto")
     */
    private $caracteristica;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="TipoAplicacion", inversedBy="presupuesto")
     * @ORM\JoinTable(name="presupuesto_tipo_aplicacion",
     *   joinColumns={
     *     @ORM\JoinColumn(name="presupuesto_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="tipo_aplicacion_id", referencedColumnName="id")
     *   }
     * )
     */
    private $tipoAplicacion;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToOne(targetEntity="Proyecto", mappedBy="presupuesto")
     */
    private $proyecto;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->caracteristica = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tipoAplicacion = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getFechaSolicitud(): ?\DateTimeInterface
    {
        return $this->fechaSolicitud;
    }

    public function setFechaSolicitud(?\DateTimeInterface $fechaSolicitud): self
    {
        $this->fechaSolicitud = $fechaSolicitud;

        return $this;
    }

    public function getFechaEntrega(): ?\DateTimeInterface
    {
        return $this->fechaEntrega;
    }

    public function setFechaEntrega(?\DateTimeInterface $fechaEntrega): self
    {
        $this->fechaEntrega = $fechaEntrega;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getOtras(): ?string
    {
        return $this->otras;
    }

    public function setOtras(?string $otras): self
    {
        $this->otras = $otras;

        return $this;
    }

    public function getPreciofinal()
    {
        return $this->preciofinal;
    }

    public function setPreciofinal($preciofinal): self
    {
        $this->preciofinal = $preciofinal;

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

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * @return Collection|Caracteristica[]
     */
    public function getCaracteristica(): Collection
    {
        return $this->caracteristica;
    }

    public function addCaracteristica(Caracteristica $caracteristica): self
    {
        if (!$this->caracteristica->contains($caracteristica)) {
            $this->caracteristica[] = $caracteristica;
            $caracteristica->addPresupuesto($this);
        }

        return $this;
    }

    public function removeCaracteristica(Caracteristica $caracteristica): self
    {
        if ($this->caracteristica->contains($caracteristica)) {
            $this->caracteristica->removeElement($caracteristica);
            $caracteristica->removePresupuesto($this);
        }

        return $this;
    }

    /**
     * @return Collection|TipoAplicacion[]
     */
    public function getTipoAplicacion(): Collection
    {
        return $this->tipoAplicacion;
    }

    public function addTipoAplicacion(TipoAplicacion $tipoAplicacion): self
    {
        if (!$this->tipoAplicacion->contains($tipoAplicacion)) {
            $this->tipoAplicacion[] = $tipoAplicacion;
        }

        return $this;
    }

    public function removeTipoAplicacion(TipoAplicacion $tipoAplicacion): self
    {
        if ($this->tipoAplicacion->contains($tipoAplicacion)) {
            $this->tipoAplicacion->removeElement($tipoAplicacion);
        }

        return $this;
    }

    public function getProyecto(): ?Proyecto
    {
        return $this->proyecto;
    }

    public function setProyecto(?Proyecto $proyecto): self
    {
        $this->proyecto = $proyecto;

//        // set (or unset) the owning side of the relation if necessary
//        $newProyecto = $proyecto === null ? null : $this;
//        if ($newProyecto !== $proyecto->getProyecto()) {
//            $proyecto->setProyecto($newProyecto);
//        }

        return $this;
    }

}
