function templating(template, obj) {
    for (i in Object.keys(obj)) {
        template = template.replace("${" + Object.keys(obj)[i] + "}", obj[Object.keys(obj)[i]])
    }
    return template;
}