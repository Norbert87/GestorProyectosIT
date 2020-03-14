<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Proyecto
 *
 * @ORM\Table(name="proyecto", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_6FD202B990119F0F", columns={"presupuesto_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProyectoRepository")
 */
class Proyecto
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
     * @var \Presupuesto
     *
     * @ORM\OneToOne(targetEntity="Presupuesto", inversedBy="proyecto")
     * @ORM\JoinColumn(name="presupuesto_id", referencedColumnName="id")
     *
     */
    private $presupuesto;

    /**
     * @var string
     *
     * @ORM\Column(name="token_publico", type="string", length=255, nullable=false)
     */
    private $tokenPublico;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255, nullable=false)
     */
    private $estado;

    /**
    * @var \Doctrine\Common\Collections\Collection
    *
    * @ORM\OneToMany(targetEntity="Tarea", mappedBy="proyecto")
    */
    private $tareas;

    /**
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Usuario")
     * @ORM\JoinTable(name="usuarios_proyectos",
     *      joinColumns={@ORM\JoinColumn(name="proyecto_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")}
     *      )
     */
    private $tecnicos;

    public function __construct()
    {
        $this->tareas = new ArrayCollection();
        $this->tecnicos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPresupuesto(): ?Presupuesto
    {
        return $this->presupuesto;
    }

    public function setPresupuesto(?Presupuesto $presupuesto): self
    {
        $this->presupuesto = $presupuesto;

        return $this;
    }

    public function getTokenPublico(): ?string
    {
        return $this->tokenPublico;
    }

    public function setTokenPublico(string $tokenPublico): self
    {
        $this->tokenPublico = $tokenPublico;

        return $this;
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

    /**
     * @return Collection|Tarea[]
     */
    public function getTareas(): Collection
    {
        return $this->tareas;
    }

    public function addTarea(Tarea $tarea): self
    {
        if (!$this->tareas->contains($tarea)) {
            $this->tareas[] = $tarea;
            $tarea->setProyecto($this);
        }

        return $this;
    }

    public function removeTarea(Tarea $tarea): self
    {
        if ($this->tareas->contains($tarea)) {
            $this->tareas->removeElement($tarea);
            // set the owning side to null (unless already changed)
            if ($tarea->getProyecto() === $this) {
                $tarea->setProyecto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getTecnicos(): Collection
    {
        return $this->tecnicos;
    }

    public function addTecnico(Usuario $tecnico): self
    {
        if (!$this->tecnicos->contains($tecnico)) {
            $this->tecnicos[] = $tecnico;
        }

        return $this;
    }

    public function removeTecnico(Usuario $tecnico): self
    {
        if ($this->tecnicos->contains($tecnico)) {
            $this->tecnicos->removeElement($tecnico);
        }

        return $this;
    }


}
