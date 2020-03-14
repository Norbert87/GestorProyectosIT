<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tarea
 *
 * @ORM\Table(name="tarea")
 * @ORM\Entity(repositoryClass="App\Repository\TareaRepository")
 */
class Tarea
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
     * @var \Proyecto
     *
     * @ORM\ManyToOne(targetEntity="Proyecto", inversedBy="tareas")
     * @ORM\JoinColumn(name="proyecto_id", referencedColumnName="id")
     */
    private $proyecto;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $tecnico;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255, nullable=false)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_corta", type="string", length=255, nullable=false)
     */
    private $descripcionCorta;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_larga", type="text", nullable=false)
     */
    private $descripcionLarga;

    /**
     * @var string
     *
     * @ORM\Column(name="notas", type="text", nullable=true)
     */
    private $notas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getDescripcionCorta(): ?string
    {
        return $this->descripcionCorta;
    }

    public function setDescripcionCorta(string $descripcionCorta): self
    {
        $this->descripcionCorta = $descripcionCorta;

        return $this;
    }

    public function getDescripcionLarga()
    {
        return $this->descripcionLarga;
    }

    public function setDescripcionLarga($descripcionLarga): self
    {
        $this->descripcionLarga = $descripcionLarga;

        return $this;
    }

    public function getNotas()
    {
        return $this->notas;
    }

    public function setNotas($notas): self
    {
        $this->notas = $notas;

        return $this;
    }

    public function getProyecto(): ?Proyecto
    {
        return $this->proyecto;
    }

    public function setProyecto(?Proyecto $proyecto): self
    {
        $this->proyecto = $proyecto;

        return $this;
    }

    public function getTecnico(): ?Usuario
    {
        return $this->tecnico;
    }

    public function setTecnico(?Usuario $tecnico): self
    {
        $this->tecnico = $tecnico;

        return $this;
    }




}