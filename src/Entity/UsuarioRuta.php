<?php

namespace App\Entity;

use App\Repository\UsuarioRutaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRutaRepository::class)]
class UsuarioRuta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'usuarioRutas')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_usuario;

    #[ORM\ManyToOne(targetEntity: RutaConInscripcion::class, inversedBy: 'usuarioRutas')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_ruta;

    #[ORM\Column(type: 'boolean')]
    private $rutero;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUsuario(): ?usuario
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(?usuario $id_usuario): self
    {
        $this->id_usuario = $id_usuario;

        return $this;
    }

    public function getIdRuta(): ?RutaConInscripcion
    {
        return $this->id_ruta;
    }

    public function setIdRuta(?RutaConInscripcion $id_ruta): self
    {
        $this->id_ruta = $id_ruta;

        return $this;
    }

    public function getRutero(): ?bool
    {
        return $this->rutero;
    }

    public function setRutero(bool $rutero): self
    {
        $this->rutero = $rutero;

        return $this;
    }
}
