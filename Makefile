clean:
	rm -rf build

build:
	mkdir build
	ppm --no-intro --compile="src/DynamicalWeb" --directory="build"

update:
	ppm --generate-package="src/DynamicalWeb"

install:
	ppm --no-intro --no-prompt --fix-conflict --install="build/net.intellivoid.dynamical_web.ppm"

install_fast:
	ppm --no-intro --no-prompt --fix-conflict --skip-dependencies --install="build/net.intellivoid.dynamical_web.ppm"