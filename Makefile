clean:
	rm -rf build

build:
	mkdir build
	ppm --no-intro --compile="src/DynamicalWeb" --directory="build"
	ppm --no-intro --compile="web_app" --directory="build"

update:
	ppm --generate-package="src/DynamicalWeb"
	ppm --generate-package="web_app"

install:
	ppm --no-intro --no-prompt --fix-conflict --install="build/net.intellivoid.dynamical_web.ppm"
	ppm --no-intro --no-prompt --fix-conflict --install="build/com.example.web_application.ppm"

install_fast:
	ppm --no-intro --no-prompt --fix-conflict --skip-dependencies --install="build/net.intellivoid.dynamical_web.ppm"
	ppm --no-intro --no-prompt --fix-conflict --skip-dependencies --install="build/com.example.web_application.ppm"