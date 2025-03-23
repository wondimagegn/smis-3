<div class="large-offset-4 large-4 columns">
    <div class="box bg-white-transparent">
        <div class="box-body" style="display: block;">
            <div class="row">
                <div class="large-12 columns">
                    <div class="row">
                        <div class="edumix-signup-panel">
                            <h6>Forgot Password?</h6>

                            <!-- Flash Messages -->
                            <?= $this->Flash->render() ?>

                            <!-- Form Begins -->
                            <?= $this->Form->create(null, ['url' => ['controller' => 'Users', 'action' => 'forget']]) ?>

                            <div class="row collapse">
                                <div class="small-2 columns">
                                    <span class="prefix bg-blue">
                                        <i class="text-white fontello-at-circled tooltipstered"></i>
                                    </span>
                                </div>
                                <div class="small-10 columns">
                                    <?= $this->Form->control('email', [
                                        'type' => 'email',
                                        'placeholder' => 'Email',
                                        'class' => 'username',
                                        'label' => false,
                                        'autocomplete' => 'off'
                                    ]) ?>
                                </div>
                            </div>

                            <div class="row collapse">
                                <div class="small-8 columns">
                                    <p>Please enter the sum of <?= h($mathCaptcha) ?></p>
                                </div>
                                <div class="small-4 columns">
                                    <?= $this->Form->control('security_code', [
                                        'label' => false,
                                        'autocomplete' => 'off'
                                    ]) ?>
                                </div>
                            </div>

                            <p><?= $this->Html->link(__('Back to Login'), ['controller' => 'Users', 'action' => 'login'], ['class' => 'forgot-button']) ?></p>

                            <div class="error-box error-message">
                                <p style="font-size: 12px; text-align: justify;">
                                    If you do not receive the email from SMiS in your inbox after submitting this form,
                                    please check your Spam and Junk folders before using this form again. <br><br>
                                    The link in the email is only valid for 30 minutes.
                                </p>
                            </div>

                            <div class="login-button">
                                <?= $this->Form->button(__('Reset Password'), ['class' => 'radius button bg-blue']) ?>
                            </div>

                            <?= $this->Form->end() ?>
                            <!-- Form Ends -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
