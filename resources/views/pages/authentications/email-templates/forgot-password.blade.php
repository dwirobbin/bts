<p>Kepada {{ $email }}</p>
<p>
    Kami menerima permintaan untuk mengatur ulang password akun yang terkait dengan {{ $email }}.
    Anda dapat mengatur ulang password dengan mengklik tombol di bawah ini:
    <br />
    <a href="{{ route('reset_password', $token) }}" target="_blank"
        style="color: #fff; border-color: #22bc66; border-style: solid; border-width: 5px 10px;
        background-color: #22bc66; display: inline-block; text-decoration: none; border-radius: 3px;
        box-shadow: 0 2px 3px rgba(0,0,0,0,16); -webkit-text-size-adjust: none; box-sizing: border-box;">
        Reset Password
    </a>
    <br />
    <b>Catatan:</b> Link ini akan valid dalam 15 menit.
    <br />
    Jika Anda tidak meminta pengaturan ulang password, harap abaikan email ini.
</p>
