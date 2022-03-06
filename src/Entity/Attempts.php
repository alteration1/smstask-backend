<?php

namespace App\Entity;

use App\Repository\AttemptsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AttemptsRepository::class)
 */
class Attempts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $attempt;

    /**
     * @ORM\ManyToOne(targetEntity=SmsText::class, inversedBy="attempts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="codeId", referencedColumnName="id")
     * })
     */
    private $codeId;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $success;

    public function __construct()
    {
        $this->attempt = new \DateTime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttempt(): ?\DateTimeInterface
    {
        return $this->attempt;
    }

    public function setAttempt(\DateTimeInterface $attempt): self
    {
        $this->attempt = $attempt;

        return $this;
    }

    public function getCodeId(): ?SmsText
    {
        return $this->codeId;
    }

    public function setCodeId(?SmsText $codeId): self
    {
        $this->codeId = $codeId;

        return $this;
    }

    public function getSuccess(): ?bool
    {
        return $this->success;
    }

    public function setSuccess(?bool $success): self
    {
        $this->success = $success;

        return $this;
    }

}
