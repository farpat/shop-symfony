<?php

namespace App\FormData;


use App\Entity\User;
use App\Services\FormData\AssertExpression;
use App\Validator\Unique;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Unique(field="email", entity="App\Entity\User")
 */
final class RegisterFormData
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $name = '';

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email = '';

    /**
     * @var bool
     * @Assert\IsTrue(message="You must agree to our terms")
     */
    private $is_agree_with_terms = false;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="6", minMessage="The password should be at least %limit% characters")
     */
    private $password = '';

    /**
     * @var string
     * @Assert\NotBlank()
     * @AssertExpression(
     *     "this.getPassword() === this.getPasswordConfirmation()",
     *     message="This string doesn't match",
     *     checkFunctionInFrontend="
    const passwordElement = document.querySelector('#register_form_password');

    if (this.tracked === undefined) {
    const passwordConfirmationElement = document.querySelector('#register_form_password_confirmation');
    passwordElement.addEventListener('blur', function (event) { passwordConfirmationElement.focus();passwordConfirmationElement.blur(); });
    this.tracked = true;
    }

    value === passwordElement.value;
    "
     * )
     */
    private $password_confirmation = '';

    /**
     * @return bool
     */
    public function getIsAgreeWithTerms(): bool
    {
        return $this->is_agree_with_terms;
    }

    /**
     * @param bool|null $is_agree_with_terms
     *
     * @return RegisterFormData
     */
    public function setIsAgreeWithTerms(?bool $is_agree_with_terms): RegisterFormData
    {
        $this->is_agree_with_terms = $is_agree_with_terms;
        return $this;
    }

    /**
     * @return string
     */
    public function getPasswordConfirmation(): ?string
    {
        return $this->password_confirmation;
    }

    /**
     * @param string $password_confirmation
     *
     * @return RegisterFormData
     */
    public function setPasswordConfirmation(?string $password_confirmation): RegisterFormData
    {
        $this->password_confirmation = $password_confirmation;
        return $this;
    }

    public function makeUser(UserPasswordEncoderInterface $passwordEncoder): User
    {
        $user = new User;
        return $user->setName($this->getName())
            ->setEmail($this->getEmail())
            ->setRoles(['ROLE_USER'])
            ->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return RegisterFormData
     */
    public function setName(?string $name): RegisterFormData
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return RegisterFormData
     */
    public function setEmail(?string $email): RegisterFormData
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return RegisterFormData
     */
    public function setPassword(?string $password): RegisterFormData
    {
        $this->password = $password;
        return $this;
    }
}