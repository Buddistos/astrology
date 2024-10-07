<div class="modal fade" tabindex="-1" id="modal_auth">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Авторизация</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <p>Выберите основное приложение для входа. Остальные сможете привязать в личном кабинете после
                    авторизации за дополнительное вознаграждение =)</p>
                <h5>Телеграм:</h5>
                <script async src="https://telegram.org/js/telegram-widget.js?22"
                        data-telegram-login="astro4me_bot" data-size="medium" data-radius="7"
                        data-auth-url="auth?method=tga" data-request-access="write"></script>
            </div>
            <div class="modal-footer">
                {{--
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                    <button type="button" class="btn btn-primary">Сохранить изменения</button>
                --}}
            </div>
        </div>
    </div>
</div>
