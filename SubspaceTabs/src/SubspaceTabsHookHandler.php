<?php

namespace MediaWiki\Extension\SubspaceTabs;

use MediaWiki\MediaWikiServices;
// use MediaWiki\Title\NamespaceInfo;
// use NamespaceInfo;
use Title;
// use Article;
// use Wikimedia\Rdbms\Subquery;

class SubspaceTabsHookHandler implements \MediaWiki\Hook\SkinTemplateNavigation__UniversalHook
{
	public function onSkinTemplateNavigation__Universal($sktemplate, &$links): void
	{
		// $config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig('subspacetabs');
		// var_dump($config);
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$children = $config->get('SubspaceTabsChildren');
		// $categories = $config->get('RandomRulesCategories');
		// $children = $config->get('SubspaceTabsChildren');
		// var_dump($links);
		// var_dump($children);
		// var_dump($sktemplate);
		$title = $sktemplate->getTitle();
		// var_dump($title);
		$parents = [];
		$namespace = $title->getNamespace();
		if ($namespace < 0) return;
		if ($namespace % 2 === 1) $namespace--;
		$newNamespace = $namespace;
		// var_dump($namespace);
		// return;
		while ($newNamespace > 0) {
			$testNamespace = $newNamespace;
			$newNamespace = -1;
			foreach ($children as $parent => $child) {
				if (in_array($testNamespace, $child)) {
					$parents[] = $parent;
					$newNamespace = $parent;
					break;
				}
			}
		}
		if ($newNamespace === -1) return; // The namespace isn't in the hierarchy at all
		if (count($parents) === 0) $parents = [0];
		// var_dump($parents);
		$tabNamespaces = [];
		// var_dump($parents[0]);
		// if ($namespace !== 0) $tabNamespaces[] = $parents[0];
		if ($namespace !== 0) $tabNamespaces = array_merge($tabNamespaces, $parents);
		// var_dump($tabNamespaces);
		// var_dump($children[$parents[0]]);
		$tabNamespaces = array_merge($tabNamespaces, $children[$parents[0]]);
		if (array_key_exists($namespace, $children) && $namespace !== 0)
			$tabNamespaces = array_merge($tabNamespaces, $children[$namespace]);
		// var_dump($tabNamespaces);
		// if ($parents[0] = )
		//	if $tabNamespaces = [$parents[0]];
		// $tabNamespaces = 

		// var_dump(array_keys($links));
		// var_dump($links["namespaces"]);
		// var_dump($links["associated-pages"]);

		$services = MediaWikiServices::getInstance();
		$namespaceInfo = $services->getNameSpaceInfo();



		foreach ($tabNamespaces as $ns) {
			//			$newTitle = Title::makeTitle($ns, $pageTitle, $anchor);
			$newTitle = Title::makeTitle($ns, $title->getBaseText());
			// var_dump($newTitle->getFullText());
			// if ($newTitle->exists())
			// var_dump($newTitle->getFullText());
			if (!$newTitle->exists()) continue;
			// var_dump($ns);
			// var_dump($namespaceInfo->getCanonicalName($ns));
			// var_dump($ns === 0 ? $title->getBaseText() : array_slice(explode("_", $namespaceInfo->getCanonicalName($ns)), -1)[0]);
			$newLink = [];
			$newLink['class'] = '';

			$newLink['text'] = ($ns === 0 ? $title->getBaseText() : array_slice(explode("_", $namespaceInfo->getCanonicalName($ns)), -1)[0]);
			$newLink['href'] = $newTitle->getLinkURL();
			$newLink['exists'] = true;
			$newLink['primary'] = true; // ???
			$newLink['context'] = $ns === 0 ? "main" : strtolower($newLink['text']); // ???
			$links["namespaces"][$newLink['context']] = $newLink;
			$links["associated-pages"][$newLink['context']] = $newLink;
			// var_dump($links["associated-pages"]);
			// if (($ns = $namespaceInfo->getCanonicalIndex(strtolower(str_replace(":", "_", $namespace)))) !== null) 
		}
	}
}
