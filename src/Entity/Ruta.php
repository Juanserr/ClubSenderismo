<?php

namespace App\Entity;

use App\Repository\RutaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RutaRepository::class)]
class Ruta
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 255)]
    private $lugar_inicio;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $lugar_fin;

    #[ORM\Column(type: 'float')]
    private $distancia;

    #[ORM\Column(type: 'float')]
    private $desnivel_acumulado;

    #[ORM\Column(type: 'string', length: 32)]
    private $duracion;

    #[ORM\Column(type: 'date')]
    private $fecha;

    #[ORM\Column(type: 'time')]
    private $hora_inicio;

    #[ORM\Column(type: 'time')]
    private $hora_fin;

    #[ORM\Column(type: 'string', length: 255)]
    private $recorrido;

    #[ORM\Column(type: 'string', length: 255)]
    private $dificultad;


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

    public function getLugarInicio(): ?string
    {
        return $this->lugar_inicio;
    }

    public function setLugarInicio(string $lugar_inicio): self
    {
        $this->lugar_inicio = $lugar_inicio;

        return $this;
    }

    public function getLugarFin(): ?string
    {
        return $this->lugar_fin;
    }

    public function setLugarFin(?string $lugar_fin): self
    {
        $this->lugar_fin = $lugar_fin;

        return $this;
    }

    public function getDistancia(): ?float
    {
        return $this->distancia;
    }

    public function setDistancia(float $distancia): self
    {
        $this->distancia = $distancia;

        return $this;
    }

    public function getDesnivelAcumulado(): ?float
    {
        return $this->desnivel_acumulado;
    }

    public function setDesnivelAcumulado(float $desnivel_acumulado): self
    {
        $this->desnivel_acumulado = $desnivel_acumulado;

        return $this;
    }

    public function getDuracion(): ?string
    {
        return $this->duracion;
    }

    public function setDuracion(string $duracion): self
    {
        $this->duracion = $duracion;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getHoraInicio(): ?\DateTimeInterface
    {
        return $this->hora_inicio;
    }

    public function setHoraInicio(\DateTimeInterface $hora_inicio): self
    {
        $this->hora_inicio = $hora_inicio;

        return $this;
    }

    public function getHoraFin(): ?\DateTimeInterface
    {
        return $this->hora_fin;
    }

    public function setHoraFin(\DateTimeInterface $hora_fin): self
    {
        $this->hora_fin = $hora_fin;

        return $this;
    }

    public function getRecorrido(): ?string
    {
        return $this->recorrido;
    }

    public function setRecorrido(string $recorrido): self
    {
        $this->recorrido = $recorrido;

        return $this;
    }

    public function getDificultad(): ?string
    {
        return $this->dificultad;
    }
    
    public function setDificultad(string $dificultad): self
    {
        $this->dificultad = $dificultad;

        return $this;
    }
}
