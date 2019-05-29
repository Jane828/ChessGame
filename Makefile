
include version.mk
BASE_VERSION=$(IMAGE_VERSION)
PREV_VERSION = 0.0.0
BASEIMAGE_RELEASE=0.0.1

ifeq ($(PROJECT_NAME),true)
PROJECT_NAME = $(PROJECT_NAME)/game
else
PROJECT_NAME = tenbayblockchain/game
endif
IS_RELEASE = true
EXPERIMENTAL ?= false

ifneq ($(IS_RELEASE),true)
EXTRA_VERSION ?= snapshot-$(shell git rev-parse --short HEAD)
PROJECT_VERSION=$(BASE_VERSION)-$(EXTRA_VERSION)
else
PROJECT_VERSION=$(BASE_VERSION)
endif

PKGNAME = gitlab.com/$(PROJECT_NAME)

include docker-env.mk

all: docker

build/bin:
	mkdir -p $@

build/bin/%: # TODO: file list
	@mkdir -p $(@D)
	@echo "$@"
	$(CGO_FLAGS) GOBIN=$(abspath $(@D)) go install -tags "$(GO_TAGS)" -ldflags "$(GO_LDFLAGS)" $(gopkgmap.$(@F))
	@echo "Binary available as $@"
	@touch $@

build/bin/%-clean:
	@echo "$@"
	$(eval TARGET = $(patsubst %-clean, %, $(@F)))
	-@rm -rf build/bin/$(TARGET) ||:
	@echo "Clean binary build/bin/$(TARGET)"

build/docker/bin/%:
	@mkdir -p $(@D)
	@echo "Building $@"
	$(CGO_FLAGS) GOBIN=$(abspath $(@D)) go install -tags "$(GO_TAGS)" -ldflags "$(GO_LDFLAGS)" $(gopkgmap.$(@F))
	@echo "Binary available as $@"
	@touch $@

build/image/front/payload: app domain poker wechat admin images/front/www.conf  
build/image/php/payload: images/php
build/image/socket/payload: socket timer images/socket/start_one.sh
build/image/mysql/payload: images/mysql/conf sql/poker_init.sql
build/image/nginx/payload: images/nginx/config admin app/files poker/all

build/image/%/payload:
	mkdir -p $@
	-cp $^ $@/ -rf

.PRECIOUS: build/image/%/Dockerfile

build/image/%/Dockerfile: images/%/Dockerfile.in
	$(eval TARGET = ${patsubst build/image/%/Dockerfile,%,${@}})
	@echo $(TARGET)
	@cat $< \
		| sed -e 's/_BASE_TAG_/$(BASE_DOCKER_TAG)/g' \
		| sed -e 's/_TAG_/$(DOCKER_TAG)/g' \
		| sed -e 's/_CAPP_NAME_/$(TARGET).exe/g' \
		| sed -e 's/_APP_NAME_/$(TARGET)/g' \
		> $@
	@echo "LABEL $(BASE_DOCKER_LABEL).version=$(PROJECT_VERSION) \\">>$@
	@echo "     " $(BASE_DOCKER_LABEL).base.version=$(BASEIMAGE_RELEASE)>>$@

build/image/php/$(DUMMY): Makefile build/image/php/payload build/image/php/Dockerfile
	$(eval TARGET = ${patsubst build/image/%/$(DUMMY),%,${@}})
	@echo "Building docker $(TARGET)-image"
	$(DBUILD) -t $(DOCKER_NS)/game-$(TARGET):1.0.0 $(@D)
	@touch $@

build/image/%/$(DUMMY): Makefile build/image/%/payload build/image/%/Dockerfile
	$(eval TARGET = ${patsubst build/image/%/$(DUMMY),%,${@}})
	@echo "Building docker $(TARGET)-image"
	$(DBUILD) -t $(DOCKER_NS)/game-$(TARGET):$(DOCKER_TAG) $(@D)
	@touch $@

.PHONY: front
front-docker: build/image/front/$(DUMMY)

.PHONY: socket
socket-docker: build/image/socket/$(DUMMY)

.PHONY: php
php-docker: build/image/php/$(DUMMY)

mysql-docker: build/image/mysql/$(DUMMY)
nginx-docker: build/image/nginx/$(DUMMY)


docker: php-docker front-docker mysql-docker nginx-docker socket-docker

%-clean: build/bin/%-clean
	@echo "End exec $@"

%-docker-clean:
	$(eval TARGET = ${patsubst %-docker-clean,%,${@}})
	-@rm -rf build/docker/bin
	#-docker images -q $(DOCKER_NS)/game-$(TARGET) | xargs -I '{}' docker rmi -f '{}'
	-@rm -rf build/image/$(TARGET) ||:

ALL_IMAGES = nginx front socket
docker-clean: $(patsubst %, %-docker-clean, $(ALL_IMAGES))

.PHONY: clean
clean: docker-clean
