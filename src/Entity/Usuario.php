<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_ADMINISTRADOR = 'ROLE_ADMINISTRADOR';
    const ROLE_CONSULTOR = 'ROLE_CONSULTOR';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_SOCIO = 'ROLE_SOCIO';
    //const exito_acceso = 'Se ha registrado con éxito';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json', nullable: true)]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string')]
    private $nombre;

    #[ORM\Column(type: 'string')]
    private $apellidos;

    #[ORM\Column(type: 'integer')]
    private $telefono;

    #[ORM\OneToMany(mappedBy: 'id_usuario', targetEntity: UsuarioRuta::class)]
    private $usuarioRutas;

    #[ORM\Column(type: 'date')]
    private $Fechaalta;

    #[ORM\Column(type: 'integer')]
    private $Validez;

    public function __construct()
    {
        $this->usuarioRutas = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getTelefono(): ?int
    {
        return $this->telefono;
    }

    public function setTelefono(int $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getFechaalta(): ?\DateTimeInterface
    {
        return $this->Fechaalta;
    }

    public function setFechaalta(\DateTimeInterface $Fechaalta): self
    {
        $this->Fechaalta = $Fechaalta;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
            $usuarioRuta->setIdUsuario($this);
        }

        return $this;
    }

    public function removeUsuarioRuta(UsuarioRuta $usuarioRuta): self
    {
        if ($this->usuarioRutas->removeElement($usuarioRuta)) {
            // set the owning side to null (unless already changed)
            if ($usuarioRuta->getIdUsuario() === $this) {
                $usuarioRuta->setIdUsuario(null);
            }
        }

        return $this;
    }

    public function esAdministrador(): bool //Comprueba que el usuario sea un Administrador
    {
        return in_array(self::ROLE_ADMINISTRADOR, $this->getRoles());
    }

    public function esEditor(): bool //Comprueba que el usuario sea un Editor
    {
        return in_array(self::ROLE_EDITOR, $this->getRoles());
    }

    public function esConsultor(): bool //Comprueba que el usuario sea un Consultor
    {
        return in_array(self::ROLE_CONSULTOR, $this->getRoles());
    }

    public function esSocio(): bool //Comprueba que el usuario sea un Socio
    {
        return in_array(self::ROLE_SOCIO, $this->getRoles());
    }

    public function esSocioValido(): bool //Comprueba que el usuario sea un Cliente validado
    {
        return (in_array(self::ROLE_SOCIO, $this->getRoles()) && ($this->getValidez() == '1'));
    }

    public function getValidez(): ?int
    {
        return $this->Validez;
    }

    public function setValidez(int $Validez): self
    {
        $this->Validez = $Validez;

        return $this;
    }

    
}
