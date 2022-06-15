<?php

namespace App\Entity;

use App\Repository\MaterialDeportivoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialDeportivoRepository::class)]
class MaterialDeportivo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 32)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 32)]
    private $marca;

    #[ORM\Column(type: 'string', length: 32)]
    private $talla;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $imagen_prenda;

    #[ORM\Column(type: 'string', length: 64)]
    private $sexo;

    #[ORM\Column(type: 'string', length: 64)]
    private $color;

    #[ORM\Column(type: 'string', length: 64)]
    private $tela;

    #[ORM\Column(type: 'date', nullable: true)]
    private $fecha_oferta;

    #[ORM\OneToMany(mappedBy: 'id_material', targetEntity: SocioMaterialdeportivo::class)]
    private $socioMaterialdeportivos;

    public function __construct()
    {
        $this->socioMaterialdeportivos = new ArrayCollection();
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

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(string $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getTalla(): ?string
    {
        return $this->talla;
    }

    public function setTalla(string $talla): self
    {
        $this->talla = $talla;

        return $this;
    }

    public function getImagenPrenda(): ?string
    {
        return $this->imagen_prenda;
    }

    public function setImagenPrenda(?string $imagen_prenda): self
    {
        $this->imagen_prenda = $imagen_prenda;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getTela(): ?string
    {
        return $this->tela;
    }

    public function setTela(string $tela): self
    {
        $this->tela = $tela;

        return $this;
    }

    public function getFechaOferta(): ?\DateTimeInterface
    {
        return $this->fecha_oferta;
    }

    public function setFechaOferta(?\DateTimeInterface $fecha_oferta): self
    {
        $this->fecha_oferta = $fecha_oferta;

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
            $socioMaterialdeportivo->setIdMaterial($this);
        }

        return $this;
    }

    public function removeSocioMaterialdeportivo(SocioMaterialdeportivo $socioMaterialdeportivo): self
    {
        if ($this->socioMaterialdeportivos->removeElement($socioMaterialdeportivo)) {
            // set the owning side to null (unless already changed)
            if ($socioMaterialdeportivo->getIdMaterial() === $this) {
                $socioMaterialdeportivo->setIdMaterial(null);
            }
        }

        return $this;
    }

}
