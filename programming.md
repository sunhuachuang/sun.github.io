---
layout: page
title: Programming
permalink: /programming/
---

{% for category in site.categories %}
  {% if category[0] == "programming" %}
  <ul class="post-list">
    {% for post in category[1] %}
      <li>
        <span class="post-meta">{{ post.date | date_to_string }}</span>
        <h3><a class="post-link" href="{{ post.url }}">{{ post.title }}</a></h3>
      </li>
    {% endfor %}
  </ul>
  {% endif %}
{% endfor %}
