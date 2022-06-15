<?php

namespace App\Entity;

use App\Repository\RutaConInscripcionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RutaConInscripcionRepository::class)]
class RutaConInscripcion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $plazas;

    #[ORM\Column(type: 'date')]
    private $fecha_socio;

    #[ORM\Column(type: 'date')]
    private $fecha_nosocio;

    #[ORM\OneToOne(targetEntity: Ruta::class, cascade: ['persist', 'remove'])]
    private $ruta;

    #[ORM\OneToMany(mappedBy: 'id_ruta', targetEntity: SocioRuta::class)]
    private $socioRutas;

    #[ORM\OneToMany(mappedBy: 'id_ruta', targetEntity: UsuarioRuta::class)]
    private $usuarioRutas;

    public function __construct()
    {
        $this->socioRutas = new ArrayCollection();
        $this->usuarioRutas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlazas(): ?int
    {
        return $this->plazas;
    }

    public function setPlazas(int $plazas): self
    {
        $this->plazas = $plazas;

        return $this;
    }

    public function getFechaSocio(): ?\DateTimeInterface
    {
        return $this->fecha_socio;
    }

    public function setFechaSocio(\DateTimeInterface $fecha_socio): self
    {
        $this->fecha_socio = $fecha_socio;

        return $this;
    }

    public function getFechaNosocio(): ?\DateTimeInterface
    {
        return $this->fecha_nosocio;
    }

    public function setFechaNosocio(\DateTimeInterface $fecha_nosocio): self
    {
        $this->fecha_nosocio = $fecha_nosocio;

        return $this;
    }

    public function getRuta(): ?Ruta
    {
        return $this->ruta;
    }

    public function setRuta(?Ruta $ruta): self
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * @return Collection<int, SocioRuta>
     */
    public function getsocioRutas(): Collection
    {
        return $this->socioRutas;
    }

    public function addsocioRutas(SocioRuta $socioRutas): self
    {
        if (!$this->socioRutas->contains($socioRutas)) {
            $this->socioRutas[] = $socioRutas;
            $socioRutas->setIdRuta($this);
        }

        return $this;
    }

    public function removesocioRutas(SocioRuta $socioRutas): self
    {
        if ($this->socioRutas->removeElement($socioRutas)) {
            // set the owning side to null (unless already changed)
            if ($socioRutas->getIdRuta() === $this) {
                $socioRutas->setIdRuta(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UsuarioRuta>
     */
    public function getUsuarioRutas(): Collection
    {
        return $this->usuarioRutas;
    }

    public function addUsuarioRuta(UsuarioRuta $usuarioRuta): self
    {
        if (!$this->usuarioRutas->contains($usuarioRuta)) {
            $this->usuarioRutas[] = $usuarioRuta;
            $usuarioRuta->setIdRuta($this);
        }

        return $this;
    }

    public function removeUsuarioRuta(UsuarioRuta $usuarioRuta): self
    {
        if ($this->usuarioRutas->removeElement($usuarioRuta)) {
            // set the owning side to null (unless already changed)
            if ($usuarioRuta->getIdRuta() === $this) {
                $usuarioRuta->setIdRuta(null);
            }
        }

        return $this;
    }

}
