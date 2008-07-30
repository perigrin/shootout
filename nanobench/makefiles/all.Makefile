# The Computer Language Benchmarks Game
# $Id: all.Makefile,v 1.1 2008-07-30 19:17:20 igouy-guest Exp $


.PHONY: benchmarks logs highlight mkcsv $(selected_source_directories) 



all: benchmarks logs highlight



# don't seem able to get both these to work? 
# just one or the highlightother which ever happens first
#benchmarks: SUBMK := benchmarks
#benchmarks: SUBROOT := $(SOURCE_ROOT)
#benchmarks: $(selected_source_directories)

#logs: SUBMK := logs
#logs: SUBROOT := $(SITE_ROOT)
#logs: $(selected_source_directories)

#highlight: SUBMK := highlight
#highlight: SUBROOT := $(SITE_ROOT)
#highlight: $(selected_source_directories)

$(SRC_DIRS):
	-@$(MAKE) -C $(SUBROOT)/$@/log \
		-f $(SITE_MAKEFILES)/Makefile_$(SITE_NAME)_$@ $(SUBMK)




benchmarks: 
	-@for d in $(SRC_DIRS) ; do \
		( $(MAKE) -C $(SOURCE_ROOT)/$$d \
                          -f $(SITE_MAKEFILES)/$(SITE_NAME)_$$d.Makefile ) ; \
		done

mkcsv:
	@$(NANO_BIN)/mkcsv


logs: 
	-@for d in $(SRC_DIRS) ; do \
		( $(MAKE) -C $(SITE_ROOT)/$$d/log \
                          -f $(MAKE_ROOT)/website.Makefile logs) ; \
		done


highlight: 
	-@for d in $(SRC_DIRS) ; do \
		( $(MAKE) -C $(SITE_ROOT)/$$d/code \
			-f $(MAKE_ROOT)/website.Makefile highlight) ; \
		done




