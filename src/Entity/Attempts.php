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
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

     /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity=Codes::class, inversedBy="attempts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="codeId", referencedColumnName="id")
     * })
     */
    private $codeId;   

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $success;

    public function __construct($phone = null, $code = null)
    {
        if($phone){
            $this->phone = $phone;        
        }
        if($code){
            $this->code = $code;
        }
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

    public function getCodeId(): ?Codes
    {
        return $this->codeId;
    }

    public function setCodeId(?Codes $codeId): self
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
