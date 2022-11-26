<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_created;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $create_by;

    /**
     * @var string
     */
    private $collabs_input;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="i_tasks")
     */
    private $collaborators;

    /**
     * @ORM\Column(type="integer")
     */
    private $pourcent;

    public function __construct()
    {
        $this->collaborators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): self
    {
        $this->date_created = $date_created;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreateBy(): ?User
    {
        return $this->create_by;
    }

    public function setCreateBy(?User $create_by): self
    {
        $this->create_by = $create_by;

        return $this;
    }

    public function getCollabsInput(): ?string
    {
        return $this->collabs_input;
    }

    public function setCollabsInput(?string $collabs_input): self
    {
        $this->collabs_input = $collabs_input;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getCollaborators(): Collection
    {
        return $this->collaborators;
    }

    public function addCollaborator(User $collaborator): self
    {
        if (!$this->collaborators->contains($collaborator)) {
            $this->collaborators[] = $collaborator;
        }

        return $this;
    }

    public function removeCollaborator(User $collaborator): self
    {
        $this->collaborators->removeElement($collaborator);

        return $this;
    }

    public function getPourcent(): ?int
    {
        return $this->pourcent;
    }

    public function setPourcent(int $pourcent): self
    {
        $this->pourcent = $pourcent;

        return $this;
    }
}
