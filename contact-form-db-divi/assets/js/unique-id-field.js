window.vendor.wp.hooks.addFilter('divi.moduleLibrary.moduleAttributes.divi.contact-form', 'divi', (attributes, metadata) => {
  attributes.module.settings.advanced.uniqueId = {
    groupType: 'group-item',
    item: {
      attrName: 'module.advanced.uniqueId',
      component: {
        name: 'divi/text',
        type: 'field'
      },
      description: 'Auto-generated unique identifier.',
      features: {
        responsive: false,
        hover: false,
        sticky: false
      },
      groupSlug: 'contentText',
      label: 'Unique ID',
      priority: 100,
      render: true,
      readonly: true
    }
  };

  return attributes;
});