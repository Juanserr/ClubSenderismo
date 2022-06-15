<?php

namespace App\Entity;

use App\Repository\SocioMaterialdeportivoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocioMaterialdeportivoRepository::class)]
class SocioMaterialdeportivo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Socio::class, inversedBy: 'socioMaterialdeportivos')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_usuario;

    #[ORM\ManyToOne(targetEntity: MaterialDeportivo::class, inversedBy: 'socioMaterialdeportivos')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_material;

    #[ORM\Column(type: 'string', length: 64)]
    private $estado;

    #[ORM\Column(type: 'date')]
    private $fecha_solicitud;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUsuario(): ?Socio
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(?Socio $id_usuario): self
    {
        $this->id_usuario = $id_usuario;

        return $this;
    }

    public function getIdMaterial(): ?MaterialDeportivo
    {
        return $this->id_material;
    }

    public function setIdMaterial(?MaterialDeportivo $id_material): self
    {
        $this->id_material = $id_material;

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

    public function getFechaSolicitud(): ?\DateTimeInterface
    {
        return $this->fecha_solicitud;
    }

    public function setFechaSolicitud(\DateTimeInterface $fecha_solicitud): self
    {
        $this->fecha_solicitud = $fecha_solicitud;

        return $this;
    }
}
