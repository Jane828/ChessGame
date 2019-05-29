
DRUN = docker run -i --rm $(DOCKER_RUN_FLAGS) \
	-v $(abspath .):$(GOPATH)/src/$(PKGNAME) \
	-w $(GOPATH)/src/$(PKGNAME)

DBUILD = sudo docker build $(DOCKER_BUILD_FLAGS)

BASE_DOCKER_NS ?= registry.cn-hangzhou.aliyuncs.com/tenbayblockchain
BASE_DOCKER_TAG=$(ARCH)-$(BASEIMAGE_RELEASE)

DOCKER_NS ?= registry.cn-hangzhou.aliyuncs.com/tenbayblockchain
DOCKER_TAG=$(PROJECT_VERSION)
PREV_TAG=$(ARCH)-$(PREV_VERSION)

BASE_DOCKER_LABEL=org.tenbayblockchain.tcc

DOCKER_DYNAMIC_LINK ?= false
DOCKER_GO_LDFLAGS += $(GO_LDFLAGS)

ifeq ($(DOCKER_DYNAMIC_LINK),false)
DOCKER_GO_LDFLAGS += -linkmode external -extldflags '-static -lpthread'
endif

#
# What is a .dummy file?
#
# Make is designed to work with files.  It uses the presence (or lack thereof)
# and timestamps of files when deciding if a given target needs to be rebuilt.
# Docker containers throw a wrench into the works because the output of docker
# builds do not translate into standard files that makefile rules can evaluate.
# Therefore, we have to fake it.  We do this by constructioning our rules such
# as
#       my-docker-target/.dummy:
#              docker build ...
#              touch $@
#
# If the docker-build succeeds, the touch operation creates/updates the .dummy
# file.  If it fails, the touch command never runs.  This means the .dummy
# file follows relatively 1:1 with the underlying container.
#
# This isn't perfect, however.  For instance, someone could delete a docker
# container using docker-rmi outside of the build, and make would be fooled
# into thinking the dependency is statisfied when it really isn't.  This is
# our closest approximation we can come up with.
#
# As an aside, also note that we incorporate the version number in the .dummy
# file to differentiate different tags to fix FAB-1145
#
DUMMY = .dummy-$(DOCKER_TAG)
