<?php

namespace App\Entity;

use App\Repository\SocioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocioRepository::class)]
class Socio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    //#[ORM\Column(type: 'string', length: 32)]
    //private $sexo;

    #[ORM\OneToOne(targetEntity: Usuario::class, cascade: ['persist', 'remove'])]
    private $usuario;

    #[ORM\OneToMany(mappedBy: 'id_usuario', targetEntity: SocioRuta::class)]
    private $socioRutas;

    #[ORM\OneToMany(mappedBy: 'id_usuario', targetEntity: SocioMaterialdeportivo::class)]
    private $socioMaterialdeportivos;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $TarjetaFederativa;

    public function __construct()
    {
        $this->socioRutas = new ArrayCollection();
        $this->socioMaterialdeportivos = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /*public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }
    */

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection<int, SocioRuta>
     */
    public function getSocioRutas(): Collection
    {
        return $this->socioRutas;
    }

    public function addSocioRuta(SocioRuta $socioRuta): self
    {
        if (!$this->socioRutas->contains($socioRuta)) {
            $this->socioRutas[] = $socioRuta;
            $socioRuta->setIdUsuario($this);
        }

        return $this;
    }

    public function removeSocioRuta(SocioRuta $socioRuta): self
    {
        if ($this->socioRutas->removeElement($socioRuta)) {
            // set the owning side to null (unless already changed)
            if ($socioRuta->getIdUsuario() === $this) {
                $socioRuta->setIdUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SocioMaterialdeportivo>
     */
    public function getSocioMaterialdeportivos(): Collection
    {
        return $this->socioMaterialdeportivos;
    }

    public function addSocioMaterialdeportivo(SocioMaterialdeportivo $socioMaterialdeportivo): self
    {
        if (!$this->socioMaterialdeportivos->contains($socioMaterialdeportivo)) {
            $this->socioMaterialdeportivos[] = $socioMaterialdeportivo;
            $socioMaterialdeportivo->setIdUsuario($this);
        }

        return $this;
    }

    public function removeSocioMaterialdeportivo(SocioMaterialdeportivo $socioMaterialdeportivo): self
    {
        if ($this->socioMaterialdeportivos->removeElement($socioMaterialdeportivo)) {
            // set the owning side to null (unless already changed)
            if ($socioMaterialdeportivo->getIdUsuario() === $this) {
                $socioMaterialdeportivo->setIdUsuario(null);
            }
        }

        return $this;
    }

    public function getTarjetaFederativa(): ?int
    {
        return $this->TarjetaFederativa;
    }

    public function setTarjetaFederativa(?int $TarjetaFederativa): self
    {
        $this->TarjetaFederativa = $TarjetaFederativa;

        return $this;
    }

}
