LoadModule wsgi_module modules/mod_wsgi.so

WSGIDaemonProcess myapp python-home=C:/PythonProject/venv python-path=C:/PythonProject

WSGIProcessGroup myapp

WSGIScriptAlias /myapp C:/PythonProject/app.wsgi

<Directory "C:/PythonProject">
    Require all granted
</Directory>